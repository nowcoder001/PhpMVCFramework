<?php
/**
 *  Yogurt : MVC Development Framework with PHP<http://www.yogurtframework.com/>
 *
 * @author          rick <158672319@qq.com>
 * @copyright		Copyright (c)2009-2013  
 * @link			http://www.yogurtframework.com
 * @license         http://www.yogurtframework.com/license/
 */
/**
 * file utils class of yogurt framework.
 * @filesource		yogurt/utils/Files.class.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */

class Files {
	private $fileName;
	private $fileSize;
	private $fileType;
	private $fileTmpName;
	
	private $targetFile; 
	
   /* public function __construct($fileField = 'file') {
    
    }*/
    
/**
 * Folder of the File
 *
 * @var Folder
 */
	var $folder = null;
/**
 * Filename
 *
 * @var string
 */
	var $name = null;
/**
 * Constructor
 *
 * @param string $path
 * @param boolean $create Create file if it does not exist
 * @return File
 */
	function __construct($path='', $create = false) {
		$this->folder = new Folder(dirname($path), $create);
		$this->name = basename($path);
		if (!$this->exists()) {
			if ($create === true) {
				if (!$this->create()) {
					return false;
				}
			} else {
				return false;
			}
		}
	}

/**
 * Append given data string to this File.
 *
 * @param string $data Data to write
 * @return boolean Success
 */
	function append($data) {
		return $this->write($data, 'a');
	}

/**
 * Get md5 Checksum of file with previous check of Filesize
 *
 * @param string $force	Data to write to this File.
 * @return string md5 Checksum {@link http://php.net/md5_file See md5_file()}
 */
	function getMd5($force = false) {
		$md5 = '';
		if ($force == true || $this->getSize(false) < MAX_MD5SIZE) {
			$md5 = md5_file($this->getFullPath());
		}
		return $md5;
	}


/**
 * Returns the filename.
 *
 * @return string The Filename
 */
	function getName() {
		return $this->name;
	}
/**
 * Returns the File's owner.
 *
 * @return int the Fileowner
 */
	function getOwner() {
		$fileowner = fileowner($this->getFullPath());
		return $fileowner;
	 }
/**
 * Returns the File group.
 *
 * @return int the Filegroup
 */
	function getGroup() {
		$filegroup = filegroup($this->getFullPath());
		return $filegroup;
	 }

/**
 * Creates the File.
 *
 * @return boolean Success
 */
	function create() {
		$dir = $this->folder->pwd();

		if (file_exists($dir) && is_dir($dir) && is_writable($dir) && !$this->exists()) {
			if (!touch($this->getFullPath())) {
				print ('[File] Could not create $this->getName()!');
				return false;
			} else {
				return true;
			}
		} else {
			print ('[File] Could not create $this->getName()!');
			return false;
		}
	}
/**
 * Returns true if the File exists.
 *
 * @return boolean
 */
	function exists() {
		$exists = file_exists($this->getFullPath());
		return $exists;
	}

/**
 * Returns true if the File is writable.
 *
 * @return boolean
 */
	function writeable() {
		$writable = is_writable($this->getFullPath());
		return $writable;
	}
/**
 * Returns true if the File is executable.
 *
 * @return boolean
 */
	function executable() {
		$executable = is_executable($this->getFullPath());
		return $executable;
	}
/**
 * Returns true if the File is readable.
 *
 * @return boolean
 */
	function readable() {
		$readable = is_readable($this->getFullPath());
		return $readable;
	}
/**
 * Returns last access time.
 *
 * @return int timestamp
 */
	function lastAccess() {
		$fileatime = fileatime($this->getFullPath());
		return $fileatime;
	 }
/**
 * Returns last modified time.
 *
 * @return int timestamp
 */
	function lastChange() {
		$filemtime = filemtime($this->getFullPath());
		return $filemtime;
	}
/**
 * Returns the current folder.
 *
 * @return Folder
 */
	function getFolder() {
		return $this->folder;
	}
/**
 * Returns the 'chmod' (permissions) of the File.
 *
 * @return string
 */
	function getChmod() {
		$substr = substr(sprintf('%o', fileperms($this->getFullPath())), -4);
		return $substr;
	}
/**
 * Returns the full path of the File.
 *
 * @return string
 */
	function getFullPath() {
		return $this->folder->slashTerm($this->folder->pwd()) . $this->getName();
	}
    
    /**
     * get file name
     * @return String
     */
    public function getFileName() {
    	return $this->fileName;
    }
    
    /**
     * get file name ext
     * @return String
     */
    public function getFileNameExt() {
    	return (substr($this->fileName, strpos($this->fileName, '.')));
    }
    
    /**
     * get file size
     * @return int
     */
    public function getFileSize() {
    	return $this->fileSize;
    }
    
    /**
     * get file type
     * @return String
     */
    public function getFileType() {
    	return $this->fileType;
    }
    
    /**
     * get target file name
     * @return String
     */
    public function getTargetFile() {
    	return $this->targetFile;
    }
    
    /**
     * set target file name
     * @param String $targetFile uploader target file
     */
    public function setTargetFile($targetFile) {
    	$this->targetFile = $targetFile;
    }
    
    /**
     * get file tmp name
     * @return String
     */
    public function getFileTmpName() {
    	return $this->fileTmpName;
    }
    
    /**
     * upload file
     * @param String $targetFile target file path
     * @return boolean
     */
    public function upload($fileField = null) {
    	
    }
    
    public function getFileExt(){
    	
    }
    
    /**
 * Returns the File extension.
 *
 * @return string The Fileextension
 */
	function getExt() {
		$ext = '';
		$parts = explode('.', $this->getName());

		if (count($parts) > 1) {
			$ext = array_pop($parts);
		} else {
			$ext = '';
		}
		return $ext;
	}
    
    /**
 * Return the contents of this File as a string.
 *
 * @return string Contents
 */
	function read() {
		$contents = file_get_contents($this->getFullPath());
		return $contents;
	}
	
	/**
 * Write given data to this File.
 *
 * @param string $data	Data to write to this File.
 * @param string $mode	Mode of writing. {@link http://php.net/fwrite See fwrite()}.
 * @return boolean Success
 */
	function write($data, $mode = 'w') {
		$file = $this->getFullPath();
		if (!($handle = fopen($file, $mode))) {
			print ('[File] Could not open $file with mode $mode!');
			return false;
		}

		if (!fwrite($handle, $data)) {
			return false;
		}

		if (!fclose($handle)) {
			return false;
		}
		return true;
	}
	
	/**
		Delete the file
		inline {@internal checks the OS php is running on, and execute appropriate command}}
		@access Public
		@return string File Content
	*/
	function del(){
		$_result=null;
		//if Windows
		if (substr(php_uname(), 0, 7) == "Windows") {
			$_filename  = str_replace( '/', '\\', $this->File_Path.$this->File_Name);
			system( 'del /F "'.$_filename.'"', $_result );
			if( $_result == 0 ){
				return true;
			} else {
				$this->_ErrCode = 'FILE_DEL'.$_result;
				return false;
			}
		//else unix assumed
		} else {
			chmod( $this->File_Path.$this->File_Name, 0775 );
			return unlink( $this->File_Path.$this->File_Name );
		}
	}
	
	/**
 * Deletes the File.
 *
 * @return boolean
 */
	function delete() {
		
		return unlink($this->getFullPath());
	 }
	
	/**
		download a file
		@access Public
		@param string [$_content] data to write into the file
		@return boolean
	*/	
	public function download(){
		header( "Content-type: ".$this->fileType );
		header( "Content-Length: ".$this->fileSize );
		header( "Content-Disposition: filename=".$this->filePath.$this->fileName );
		header( "Content-Description: Download Data" );
		echo $this->Content;
	}
	
	/**
 * Returns the Filesize, either in bytes or in human-readable format.
 *
 * @param boolean $humanReadeble	Data to write to this File.
 * @return string|int filesize as int or as a human-readable string
 */
	function getSize() {
		$size = filesize($this->getFullPath());
		return $size;
	}
	
	
}

class Folder {
/**
 * Path to Folder.
 *
 * @var string
 */
	var $path = null;
/**
 * Sortedness.
 *
 * @var boolean
 */
	var $sort = false;
/**
 * Constructor.
 *
 * @param string $path
 * @param boolean $path
 */
	function __construct($path = false, $create = false, $mode = false) {
		if (empty($path)) {
			$path = getcwd();
		}

		if (!file_exists($path) && $create == true) {
			$this->mkdirr($path, $mode);
		}
		$this->cd($path);
	}
/**
 * Return current path.
 *
 * @return string Current path
 */
	function pwd() {
		return $this->path;
	}
/**
 * Change directory to $desired_path.
 *
 * @param string $desired_path Path to the directory to change to
 * @return string The new path. Returns false on failure
 */
	function cd($desiredPath) {
		$desiredPath = realpath($desiredPath);
		$newPath = $this->isAbsolute($desiredPath) ? $desiredPath : $this->addPathElement($this->path, $desiredPath);
		$isDir = (is_dir($newPath) && file_exists($newPath)) ? $this->path = $newPath : false;
		return $isDir;
	 }
/**
 * Returns an array of the contents of the current directory, or false on failure.
 * The returned array holds two arrays: one of dirs and one of files.
 *
 * @param boolean $sort
 * @param boolean $noDotFiles
 * @return array
 */
	function ls($sort = true, $noDotFiles = false) {
		$dirs = $files = array();
		$dir = opendir($this->path);
		if ($dir) {
			while(false !== ($n = readdir($dir))) {
				if ((!preg_match('#^\.+$#', $n) && $noDotFiles == false) || ($noDotFiles == true && !preg_match('#^\.(.*)$#', $n))) {
					if (is_dir($this->addPathElement($this->path, $n))) {
						$dirs[] = $n;
					} else {
						$files[] = $n;
					}
				}
			}

			if ($sort || $this->sort) {
				sort ($dirs);
				sort ($files);
			}
			closedir ($dir);
		} 
		return array($dirs,$files);
	}
/**
 * Returns an array of all matching files in current directory.
 *
 * @param string $pattern Preg_match pattern (Defaults to: .*)
 * @return array
 */
	function find($regexp_pattern = '.*') {
		$data = $this->ls();

		if (!is_array($data)) {
			return array();
		}

		list($dirs, $files) = $data;
		$found =  array();

		foreach($files as $file) {
			if (preg_match("/^{$regexp_pattern}$/i", $file)) {
				$found[] = $file;
			}
		}
		return $found;
	}
/**
 * Returns an array of all matching files in and below current directory.
 *
 * @param string $pattern Preg_match pattern (Defaults to: .*)
 * @return array Files matching $pattern
 */
	function findRecursive($pattern = '.*') {
		$startsOn = $this->path;
		$out = $this->_findRecursive($pattern);
		$this->cd($startsOn);
		return $out;
	}
/**
 * Private helper function for findRecursive.
 *
 * @param string $pattern
 * @return array Files matching pattern
 * @access private
 */
	function _findRecursive($pattern) {
		list($dirs, $files) = $this->ls();
		
		$found = array();
		foreach($files as $file) {
			if (preg_match("/^{$pattern}$/i", $file)) {
				$found[] = $this->addPathElement($this->path, $file);
			}
		}
		$start = $this->path;
		foreach($dirs as $dir) {
			$this->cd($this->addPathElement($start, $dir));
			$found = array_merge($found, $this->findRecursive($pattern));
		}
		return $found;
	}
/**
 * Returns true if given $path is a Windows path.
 *
 * @param string $path Path to check
 * @return boolean
 * @static
 */
	function isWindowsPath($path) {
		$match = preg_match('#^[A-Z]:\\\#i', $path) ? true : false;
		return $match;
	}
/**
 * Returns true if given $path is an absolute path.
 *
 * @param string $path Path to check
 * @return boolean
 * @static
 */
	function isAbsolute($path) {
		 
		return preg_match('#^\/#', $path) || preg_match('#^[A-Z]:\\\#i', $path);
	}
/**
 * Returns true if given $path ends in a slash (i.e. is slash-terminated).
 *
 * @param string $path Path to check
 * @return boolean
 * @static
 */
	function isSlashTerm($path) {
		return preg_match('#[\\\/]$#', $path) ? true : false;		  
	}
/**
 * Returns a correct set of slashes for given $path. (\\ for Windows paths and / for other paths.)
 *
 * @param string $path Path to check
 * @return string Set of slashes ("\\" or "/")
 * @static
 */
	function correctSlashFor($path) {
		return $this->isWindowsPath($path) ? '\\' : '/';
	}
/**
 * Returns $path with added terminating slash (corrected for Windows or other OS).
 *
 * @param string $path Path to check
 * @return string
 * @static
 */
function slashTerm($path) {
		  return $path . ($this->isSlashTerm($path) ? null : $this->correctSlashFor($path));
	 }
/**
 * Returns $path with $element added, with correct slash in-between.
 *
 * @param string $path
 * @param string $element
 * @return string
 * @static
 */
	function addPathElement($path, $element) {
		return $this->slashTerm($path) . $element;
	}

/**
 * Returns true if the File is in given path.
 *
 * @return boolean
 */
	function inPath($path = '') {
		$dir = substr($this->slashTerm($path), 0, -1);
		$return = preg_match('/^' . preg_quote($this->slashTerm($dir), '/') . '(.*)/', $this->slashTerm($this->pwd()));
		if ($return == 1) {
			return true;
		} else {
			return false;
		}
	}
/**
 * Create a directory structure recursively.
 *
 * @param string $pathname The directory structure to create
 * @return bool Returns TRUE on success, FALSE on failure
 */
	function mkdirr($pathname, $mode = 0777) {
		if (is_dir($pathname) || empty($pathname)) {
			return true;
		}

		if (is_file($pathname)) {
			trigger_error('mkdirr() File exists', E_USER_WARNING);
			return false;
		}
		/*return is_dir($pathname) or (self::mkdirr(dirname($pathname)) and mkdir($pathname, $mode)); */
		$nextPathname = substr($pathname, 0, strrpos($pathname, DIRECTORY_SEPARATOR));
           
		if ($this->mkdirr($nextPathname, $mode)) {
			if (!file_exists($pathname)) {
				umask (0);
				$mkdir = mkdir($pathname, $mode);
				return $mkdir;
			}
		}
		return false;
	}
	
	/*function mkrdir($dir,$mode = 0777){       
    	 return is_dir($dir) or (self::mkrdir(dirname($dir)) and mkdir($dir, $mode));   
    } */
/**
 * Returns the size in bytes of this Folder.
 *
 * @param string $directory Path to directory
 */
	function dirsize() {
		$size = 0;
		$directory = $this->slashTerm($this->path);
		$stack = array($directory);
		$count = count($stack);
		for($i = 0, $j = $count; $i < $j; ++$i) {
			if (is_file($stack[$i])) {
				$size += filesize($stack[$i]);
			} elseif (is_dir($stack[$i])) {
				$dir = dir($stack[$i]);

				while(false !== ($entry = $dir->read())) {
					if ($entry == '.' || $entry == '..') {
						continue;
					}
					$add = $stack[$i] . $entry;

					if (is_dir($stack[$i] . $entry)) {
						$add = $this->slashTerm($add);
					}
					$stack[ ]= $add;
				}
				$dir->close();
			}
			$j = count($stack);
		}
		return $size;
	}
	
	function delDir($patch=null){
			if (is_dir($patch) || empty($patch)) {
			return true;
		}

	return rmdir($patch);
	}
}

class Export{
	
	
}
?>


<?php
/*
 * Created on 2011-3-20
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class IniParser {
	
	var $_iniFilename = '';
	var $_iniParsedArray = array();
	
	/** 
	*  erstellt einen mehrdimensionalen Array aus der INI-Datei
	**/
	function IniParser( $filename )
	{
		$this->_iniFilename = $filename;
		if($this->_iniParsedArray = parse_ini_file( $filename, true ) ) {
			return true;
		} else {
			return false;
		} 
	}
	
	/**
	* gibt die komplette Sektion
	**/
	function getSection( $key )
	{
		return $this->_iniParsedArray[$key];
	}
	
	/**
	*  gibt einen Wert aus einer Sektion 
	**/
	function getValue( $section, $key )
	{
		if(!isset($this->_iniParsedArray[$section])) return false;
		return $this->_iniParsedArray[$section][$key];
	}
	
	/**
	*  gibt den Wert einer Sektion  oder die ganze Section
	**/
	function get( $section, $key=NULL )
	{
		if(is_null($key)) return $this->getSection($section);
		return $this->getValue($section, $key);
	}
	
	/**
	* Seta um valor de acordo com a chave especificada
	**/
	function setSection( $section, $array )
	{
		if(!is_array($array)) return false;
		return $this->_iniParsedArray[$section] = $array;
	}
	
	/**
	* setzt einen neuen Wert in einer Section
	**/
	function setValue( $section, $key, $value )
	{
		if( $this->_iniParsedArray[$section][$key] = $value ) return true;
	}
	
	/**
	* setzt einen neuen Wert in einer Section oder eine gesamte, neue Section
	**/
	function set( $section, $key, $value=NULL )
	{
		if(is_array($key) && is_null($value)) return $this->setSection($section, $key);
		return $this->setValue($section, $key, $value);
	}
	
	/**
	* sichert den gesamten Array in die INI-Datei
	**/
	function save( $filename = null )
	{
		if( $filename == null ) $filename = $this->_iniFilename;
		if( is_writeable( $filename ) ) {
			$SFfdescriptor = fopen( $filename, "w" );
			foreach($this->_iniParsedArray as $section => $array){
				fwrite( $SFfdescriptor, "[" . $section . "]\n" );
				foreach( $array as $key => $value ) {
					fwrite( $SFfdescriptor, "$key = $value\n" );
				}
				fwrite( $SFfdescriptor, "\n" );
			}
			fclose( $SFfdescriptor );
			return true;
		} else {
			return false;
		}
	}
}
?>
<?php
/*

include("class.iniparser.php");

$cfg = new IniParser("config.ini");

$name = $cfg->get("Owner","firstname")." ".$cfg->get("Owner","lastname");
echo $name;

$tool = $cfg->get("Tool");
print_r($tool);
$cfg->setValue("Tool","version", "0.9beta");

print_r($cfg);

config.ini:
[Tool]
name = mein kleiner Parser
version = 0.9alpha
lastmodified = 2006-01-16

[Owner]
firstname = Enrico
lastname = Reinsdorf
email = enrico@re-design.de
web = http://re-design.de

*/
?>