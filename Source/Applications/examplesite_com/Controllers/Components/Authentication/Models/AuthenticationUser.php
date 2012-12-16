<?php

abstract class ComponentDataObject extends DataObject{
	
}

// i dont love where this ended up because im still conflicted on having models and actions outside of the sites scope. There arent any namespaces yet. it seems like a mistake.
// also, i can totally live with loading them through the parent controller as i've been doing and they could be copied into models on install.
