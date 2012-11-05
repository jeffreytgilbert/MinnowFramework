This is work in progress, much needs to be done.

Sorry about the poor documentation, I am going to try and expand on this when I get a chance.

The way that I use this class is by creating a model.php inside APPPATH.

class Model extends Kohana_Model
{
	protected $memcache;
	
	function __construct()
	{
		$memcache_config = Kohana::$config->load('cache')->default;
		
		$this->memcache = cache::instance();
		$this->memcache->connect($memcache_config);
	}
}

Config file, supports multiple clusters and multiple memcache hosts

return array
(
	'default' => array
	(
		0 => array
		(
			'host' => '127.0.0.1',
			'port' => 11211,
		),
	)
);

