<?php
/*
PHPDoctor: The PHP Documentation Creator
Copyright (C) 2004 Paul James <paul@peej.co.uk>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/** Represents a PHP package. Provides access to information about the package,
 * the package's comment and tags, and the classes in the package.
 *
 * @package PHPDoctor
 */
class packageDoc extends doc {

	/** Reference to the root element.
	 *
	 * @var rootDoc
	 */
	var $_root = NULL;

	/** The classes in this package
	 *
	 * @var classDoc[]
	 */
	var $_classes = array();

	/** The globals in this package
	 *
	 * @var fieldDoc[]
	 */
	var $_globals = array();

	/** The functions in this package
	 *
	 * @var methodDoc[]
	 */
	var $_functions = array();
	
	/** Constructor
	 *
	 * @param rootDoc root
	 * @param str name
	 */
	function packageDoc(&$root, $name) {
		$this->_name = $name;
		$this->_root =& $root;
		
		$phpdoctor =& $root->phpdoctor();
		$options =& $phpdoctor->options();
		
		// parse overview file
		if (isset($options['packageCommentDir'])) {
			$overviewFile = $options['packageCommentDir'].$this->_name.'html';
		} else {
			$overviewFile = $this->asPath().$this->_name.'html';
		}
		if (is_file($overviewFile)) {
			$phpdoctor->message('Reading package overview file "'.$options['overview'].'".');
			if ($html = $this->getHTMLContents($overviewFile)) {
				$this->_data = $phpdoctor->processDocComment('/** '.$html.' */');
				$this->_mergeData();
			}
		}

	}
	
	/** Return the package path.
	 *
	 * @return str
	 */
	function asPath() {
		$phpdoctor =& $this->_root->phpdoctor();
		return $phpdoctor->sourcePath().str_replace('.', '/', str_replace('\\', '/', $this->_name));
	}
	
	/** Add a class to this package.
	 *
	 * @param classDoc class
	 */
	function addClass(&$class) {
		$this->_classes[$class->name()] =& $class;
	}

	/** Add a global to this package.
	 *
	 * @param fieldDoc global
	 */
	function addGlobal(&$global) {
		$this->_globals[$global->name()] =& $global;
	}

	/** Add a function to this package.
	 *
	 * @param methodDoc function
	 */
	function addFunction(&$function) {
		$this->_functions[$function->name()] =& $function;
	}
	
	/** Get all included classes (including exceptions and interfaces).
	 *
	 * @return classDoc[] An array of classes
	 */
	function &allClasses() {
		return $this->_classes;
	}

	/** Get exceptions in this package.
	 *
	 * @return classDoc[] An array of exceptions
	 */
	function &exceptions() {
		$exceptions = NULL;
		foreach ($this->_classes as $name => $class) {
			if ($class->isException()) {
				$exceptions[$name] =& $class;
			}
		}
		return $exceptions;
	}

	/** Get interfaces in this package.
	 *
	 * @return classDoc[] An array of interfaces
	 */
	function &interfaces() {
		$interfaces = NULL;
		foreach ($this->_classes as $name => $class) {
			if ($class->isInterface()) {
				$interfaces[$name] =& $class;
			}
		}
		return $interfaces;
	}

	/** Get ordinary classes (excluding exceptions and interfaces) in this package.
	 *
	 * @return classDoc[] An array of classes
	 */
	function &ordinaryClasses() {
		$classes = NULL;
		foreach ($this->_classes as $name => $class) {
			if ($class->isOrdinaryClass()) {
				$classes[$name] =& $class;
			}
		}
		return $classes;
	}
	
	/** Get globals in this package.
	 *
	 * @return fieldDoc[] An array of globals
	 */
	function &globals() {
		return $this->_globals;
	}

	/** Get functions in this package.
	 *
	 * @return methodDoc[] An array of functions
	 */
	function &functions() {
		return $this->_functions;
	}

	/** Lookup for a class within this package.
	 *
	 * @param str className Name of the class to lookup
	 * @return classDoc A class
	 */
	function &findClass($className) {
		if (isset($this->_classes[$className])) {
			$class =& $this->_classes[$className];
		} else {
			$class = NULL;
		}
		return $class;
	}

}

?>