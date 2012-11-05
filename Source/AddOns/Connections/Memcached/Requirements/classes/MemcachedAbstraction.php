<?php

/*******************************************************************************\
* Joped's memcache wrapper                                                      *
* This PHP class handles both memcache and memcached bindings                   *
* Written by Joseph Engo                                                        *
* https://github.com/jengo/PHP-memcache-wrapper                                 *
* ----------------------------------------------------------------------------- *
* This library is free software; you can redistribute it and/or                 *
* modify it under the terms of the GNU Lesser General Public                    *
* License as published by the Free Software Foundation; either                  *
* version 2.1 of the License, or (at your option) any later version.            *
*                                                                               *
* This library is distributed in the hope that it will be useful,               *
* but WITHOUT ANY WARRANTY; without even the implied warranty of                *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU             *
* Lesser General Public License for more details.                               *
*                                                                               *
* You should have received a copy of the GNU Lesser General Public              *
* License along with this library; if not, write to the Free Software           *
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301 USA *
*                                                                               *
\*******************************************************************************/

class MemcachedAbstraction
{
	static $_instance                = array();
	private $memcache                = NULL;
	private $flags;
	
	var $use_compression             = false;
	
	// If you turn this off, and don't have a memcache library installed this will generate a lot of errors
	var $ignore_connection_failures  = true;
	var $max_expire                  = 604800;
	var $default_expire              = 3600;
	var $default_failed_return       = NULL;
	
	// Prevent duplicate over the wire requests
	// Caution, this does NOT respect key expirations!
	// You should not use this feature for long running processes
	// or you might encouter memory issues
	// This option is also not supported by all features
	var $use_internal_cache          = true;
	var $cached                      = array();
	
	var $library_order_search        = array('memcached', 'memcache');
	var $library                     = NULL;
	
	var $default_weight              = 1;
	
	var $command_counters = array
	(
		'add'          => 0,
		'append'       => 0,
		'decrement'    => 0,
		'delete'       => 0,
		'fetch'        => 0,
		'fetchAll'     => 0,
		'flush'        => 0,
		'get'          => 0,
		'get_delayed'  => 0,
		'get_list'     => 0,
		'increment'    => 0,
		'prepend'      => 0,
		'replace'      => 0,
		'set'          => 0
	);
	
	var $hit_counters = array
	(
		'internal_hit' => 0,
		'hit'          => 0,
		'miss'         => 0
	);

	// Allows multiple instances, for example separating sessions
	static function instance($instance_name = 'default')	
	{
		if (! isset(self::$_instance[$instance_name]) || !(self::$_instance[$instance_name] instanceof MemcachedAbstraction))
		{
			self::$_instance[$instance_name] = $Instance = new MemcachedAbstraction;
		}
		
		return $Instance;
	}
	
	// failure_callback is not supported by the memcached library
	function connect(array $servers, $failure_callback = NULL)
	{
		foreach ((array)$this->library_order_search as $library)
		{
			if ($this->library === NULL && class_exists($library))
			{
				$this->memcache = new $library;
				$this->library  = $library;
			}
		}

		if (! $this->library)
		{
			trigger_error('No suitable memcache library could be found', E_USER_WARNING);
			
			return false;
		}

		$this->set_compression($this->use_compression);

		foreach ($servers as $connection_name => $server)
		{
			if(!isset($server['weight']))
			{
				$server['weight'] = $this->default_weight;
			}
			
			$persistent = false;
			if (isset($server['persistent']))
			{
				$persistent = true;
			}
			
			if ($this->library == 'memcache')
			{
				$this->memcache->addServer($server['host'], $server['port'], $persistent, $server['weight'], NULL, NULL, NULL, $failure_callback);
			}
			
			if ($this->library == 'memcached')
			{
				$this->memcache->addServer($server['host'], $server['port'], $server['weight']);
			}
		}
	}
	
	function add($key, $value, $expire = NULL)
	{
		if ($this->ignore_connection_failures && ! $this->memcache)
		{
			return $this->default_failed_return;
		}

		$expire = $this->expire($expire);
		
		if ($this->library == 'memcached')
		{
			$result = $this->memcache->add($key, $value, $expire);
		}
		
		if ($this->library == 'memcache')
		{
			$result = $this->memcache->add($key, $value, $this->flags, $expire);
		}
		
		if ($this->use_internal_cache && $result)
		{
			$this->cached[$key] = $value;
		}
		
		return $result;
	}

	function append($key, $value)
	{
		// We don't know what state the value is in
		// remove it from internal cache and a fresh get will populate it
		if ($this->ignore_connection_failures && ! $this->memcache)
		{
			return $this->default_failed_return;
		}
		
		if ($this->use_internal_cache)
		{
			unset($this->cached[$key]);
		}

		$this->command_counters['append']++;

		// Only memcached supports this
		if ($this->library == 'memcached')
		{
			return $this->memcache->append($key, $value);
		}
		else
		{
			return $this->default_failed_return;
		}
	}
	
	function cas($cas_token, $key, $value, $expire = NULL)
	{
	
	}
	
	function decrement($key, $numeric_value)
	{
		if ($this->ignore_connection_failures && ! $this->memcache)
		{
			return $this->default_failed_return;
		}
		
		$this->command_counters['decrement']++;

		$result = $this->memcache->decrement($key, $numeric_value);

		if ($this->use_internal_cache)
		{
			$this->cached[$key] = $result;
		}

		return $result;
	}

	function delete($key)
	{
		if ($this->ignore_connection_failures && ! $this->memcache)
		{
			return $this->default_failed_return;
		}
		
		if ($this->use_internal_cache)
		{
			unset($this->cached[$key]);
		}

		$this->command_counters['delete']++;

		return $this->memcache->delete($key);
	}
	
	private function expire($expire)
	{
		if (! $expire)
		{
			$expire = $this->default_expire;
		}
		
		if ($expire > $this->max_expire)
		{
			$expire = $this->max_expire;
		}
		
		return $expire;
	}
	
	function fetch()
	{
		if ($this->ignore_connection_failures && ! $this->memcache)
		{
			return $this->default_failed_return;
		}

		$this->command_counters['fetch']++;

		if ($this->library == 'memcached')
		{
			$result = $this->memcache->fetch();

			if ($result)
			{
				$this->hit_counters['hit']++;
			}
			else
			{
				$this->hit_counters['miss']++;
			}
			
			if ($this->use_internal_cache)
			{
				$this->cached[$result['key']] = $result['value'];
			}
			
			return $result;
		}
		else
		{
			return $this->default_failed_return;
		}
	}

	function fetchAll()
	{
		if ($this->ignore_connection_failures && ! $this->memcache)
		{
			return $this->default_failed_return;
		}

		$this->command_counters['fetchAll']++;

		if ($this->library == 'memcached')
		{
			$this->hit_counters['hit']++;

			$result = $this->memcache->fetchAll();
			
			if ($result)
			{
				$this->hit_counters['hit']++;
			}
			else
			{
				$this->hit_counters['miss']++;
			}

			if ($this->use_internal_cache)
			{
				foreach ((array)$result as $v)
				{
					$this->cached[$v['key']] = $v['value'];
				}
			}
			
			return $result;
		}
		else
		{
			return $this->default_failed_return;
		}
	}

	function flush()
	{
		if ($this->ignore_connection_failures && ! $this->memcache)
		{
			return $this->default_failed_return;
		}

		$this->command_counters['flush']++;

		if ($this->use_internal_cache)
		{
			$this->cached = array();
		}
		
		return $this->memcache->flush();
	}

	function get($key)
	{
		if ($this->ignore_connection_failures && ! $this->memcache)
		{
			return $this->default_failed_return;
		}

		$this->command_counters['get']++;

		if ($this->use_internal_cache && isset($this->cached[$key]))
		{
			$this->hit_counters['internal_hit']++;

			return $this->cached[$key];
		}

		$result = $this->memcache->get($key);

		if ($result)
		{
			$this->hit_counters['hit']++;

			if ($this->use_internal_cache)
			{
				$this->cached[$key] = $result;
			}

			return $result;
		}
		else
		{			
			$this->hit_counters['miss']++;

			return $this->default_failed_return;
		}
	}
	
	// Only memcached supports $cas_tokens
	function get_list(array $keys, $cas_tokens = NULL, $flags = NULL)
	{
		if ($this->ignore_connection_failures && ! $this->memcache)
		{
			return $this->default_failed_return;
		}

		$internal_cache_results = array();
		$get_results            = array();
		$remaining_keys         = $keys;

		if ($this->use_internal_cache)
		{
			foreach ($keys as $i => $key)
			{
				if (array_key_exists($key, $this->cached))
				{
					unset($remaining_keys[$i]);
					$internal_cache_results[$key] = $this->cached[$key];

					$this->hit_counters['internal_hit']++;
				}
			}
		}
		
		$this->command_counters['get_list']++;

		if ($this->library == 'memcache')
		{
			$get_results = $this->memcache->get($remaining_keys);
		}

		if ($this->library == 'memcached')
		{
			$get_results = $this->memcache->getMulti($remaining_keys, $cas_tokens, $flags);
		}

		$result = array_merge($internal_cache_results, (array)$get_results);

		foreach ((array)$remaining_keys as $i => $remaining_key)
		{
			if (! isset($result[$remaining_key]))
			{
				$this->cached[$remaining_key] = $this->default_failed_return;

				$this->hit_counters['miss']++;
			}
		}

		foreach ((array)$get_results as $get_result_key => $get_result_value)
		{
			// Empty key shows up sometimes
			if ($get_result_key)
			{
				$this->cached[$get_result_key] = $get_result_value;
			}
		}

		$this->hit_counters['hit'] += count($get_results);
		
		return $result;
	}
	
	function getDelayed(array $keys, $with_cas = false, $callback = NULL)
	{
		if ($this->ignore_connection_failures && ! $this->memcache)
		{
			return $this->default_failed_return;
		}

		$this->command_counters['get_delayed']++;

		if ($this->library == 'memcached')
		{
			return $this->memcache->getDelayed($keys, $with_cas, $callback);
		}		
	}
	
	function getResultCode()
	{
		if ($this->library == 'memcached')
		{
			return $this->memcache->getResultCode();
		}
	}
	
	function getResultMessage()
	{
		if ($this->library == 'memcached')
		{
			return $this->memcache->getResultMessage();
		}
	}
	
	function getStats()
	{
		if ($this->library == 'memcached')
		{
			return $this->memcache->getStats();
		}
	}
	
	function increment($key, $numeric_value)
	{
		if ($this->ignore_connection_failures && ! $this->memcache)
		{
			return $this->default_failed_return;
		}
		
		$this->command_counters['increment']++;

		$result = $this->memcache->increment($key, $numeric_value);

		if ($this->use_internal_cache)
		{
			$this->cached[$key] = $result;
		}

		return $result;
	}
	
	function prepend($key, $value)
	{
		// We don't know what state the value is in
		// remove it from internal cache and a fresh get will populate it
		if ($this->ignore_connection_failures && ! $this->memcache)
		{
			return $this->default_failed_return;
		}
		
		if ($this->use_internal_cache)
		{
			unset($this->cached[$key]);
		}

		$this->command_counters['prepend']++;

		// Only memcached supports this
		if ($this->library == 'memcached')
		{
			return $this->memcache->prepend($key, $value);
		}
		else
		{
			return $this->default_failed_return;
		}
	}

	function replace($key, $value, $expire = NULL)
	{
		if ($this->ignore_connection_failures && ! $this->memcache)
		{
			return $this->default_failed_return;
		}
		
		$expire = $this->expire($expire);
		
		$this->command_counters['replace']++;

		$result = $this->memcache->replace($key, $value, $expire);
		
		if ($result && $this->use_internal_cache)
		{
			$this->cached[$key] = $value;
		}
		
		return $result;
	}
	
	function set($key, $value, $expire = NULL)
	{
		if (! $this->memcache)
		{
			return $this->default_failed_return;
		}

		$expire = $this->expire($expire);
		
		$this->command_counters['set']++;
				
		if ($this->use_internal_cache)
		{
			$this->cached[$key] = $value;
		}
		
		if ($this->library == 'memcached')
		{
			return $this->memcache->set($key, $value, $expire);
		}
		
		if ($this->library == 'memcache')
		{
			return $this->memcache->set($key, $value, $this->flags, $expire);		
		}
	}

	function set_compression($enable_compression = true)
	{
		if ($this->library == 'memcached')
		{
			if ($enable_compression)
			{
				$this->memcache->setOption(Memcached::OPT_COMPRESSION, true);
				$this->use_compression = true;
			}
			else
			{
				$this->memcache->setOption(Memcached::OPT_COMPRESSION, false);
				$this->use_compression = false;
			}
		}

		if ($this->library == 'memcache')
		{
			if ($enable_compression)
			{
				$this->flags           = MEMCACHE_COMPRESSED;
				$this->use_compression = true;
			}
			else
			{
				$this->flags           = NULL;
				$this->use_compression = false;
			}
		}
	}

	function set_list(array $items, $expire = NULL)
	{
		
	}
}

