<?php defined('SYSPATH') OR die('No direct script access.');

class Meta extends Kohana_Meta {
	/**
	 * Loads tags from config.
	 * 
	 *     Meta::instance()->load_from_config('cms.meta_tags');
	 *     Meta::instance()->load_from_config(['meta_tags', 'blog.meta']);
	 * 
	 * @param   string|array  $group  Configuration name or an array of them
	 * @return  $this
	 * @uses    Kohana::$config
	 * @uses    Config::load
	 * @uses    Config_Group::as_array
	 */
	public function load_from_config($groups)
	{
		$tags = array();
		$attribute = 'name';
		foreach ( (array) $groups as $group)
		{
			if (is_array($group))
			{
				$attribute = $group['attribute'];
				$group = $group['name'];
			}
			// Loads config
			$config = Kohana::$config->load($group);
			if ($config instanceof Config_Group)
			{
				$config = $config->as_array();
			}
			// Sets loaded tags
			$this->set($config, NULL, $attribute);
		}
		// Returns self
		return $this;
	}
	
	/**
	 * Sets tags.
	 * 
	 * @param  string|array  $name   Tag name or array of tags
	 * @param  string|array  $value  Content attribute value
	 * @param  string|array  $attribute Attribute 'name' or RDFa 'property'
	 * @return Meta
	 * @uses   Arr::is_array
	 * @uses   UTF8::strtolower
	 */
	public function set($name, $value = NULL, $attribute = 'name')
	{
		if ( ! Arr::is_array($name))
		{
			$name = array($name => $value);
		}
		// Sets tags
		foreach ($name as $tag => $value)
		{
			$tag = UTF8::strtolower($tag);
			if ($tag === 'title')
			{
				// Sets title tag
				$this->_tags['title'] = $value;
			}
			else
			{
				if (isset($this->_tags[$tag]))
				{
					// Updates tag
					$this->_tags[$tag]['content'] = $value;
				}
				elseif (($this->_cfg['hide_empty'] === TRUE AND ! empty($value)) OR $this->_cfg['hide_empty'] !== TRUE)
				{
					// Adds tag
					$group = in_array($tag, $this->_cfg['http-equiv']) ? 'http-equiv' : $attribute;
					$this->_tags[$tag] = array($group => $tag, 'content' => $value);
				}
			}
		}
		return $this;
	}
}