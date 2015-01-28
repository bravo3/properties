<?php
namespace Bravo3\Properties;

use Bravo3\Properties\Exception\PropertyNotFoundException;
use Bravo3\Properties\Exception\ReadOnlyException;
use Bravo3\Properties\Exception\UnreadableConfigException;
use Symfony\Component\Yaml\Yaml;

/**
 * Property loader
 */
class Conf implements \ArrayAccess
{

    /**
     * @var string[]
     */
    protected $data;

    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @var Conf
     */
    protected static $instance = null;

    protected function __construct($path, $fn, $delimiter)
    {
        $this->delimiter = $delimiter;

        if (substr($path, -1) != DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }

        $conf_file = new \SplFileInfo($path . $fn);

        if (!$conf_file->isReadable()) {
            $conf_file = new \SplFileInfo($path . $fn . '.dist');

            if (!$conf_file->isReadable()) {
                throw new UnreadableConfigException($conf_file->getRealPath());
            }
        }

        $this->data = Yaml::parse(file_get_contents($conf_file->getRealPath()));
    }

    protected function __clone()
    {
        // here be dragons!
    }

    public static function init($path, $fn = 'properties.yml', $delimiter = '.')
    {
        static::$instance = new self($path, $fn, $delimiter);
    }

    public static function getInstance()
    {
        if (!static::$instance) {
            static::init(getcwd());
        }

        return static::$instance;
    }

    /**
     * Retrieve a property
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $instance = static::getInstance();

        if (!$instance->offsetExists($key)) {
            return $default;
        }

        return $instance->offsetGet($key);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     * @return boolean true on success or false on failure.
     *                      </p>
     *                      <p>
     *                      The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        $path = explode($this->delimiter, $offset);

        $value = $this->data;
        foreach ($path as $index) {
            if (isset($value[$index])) {
                $value = $value[$index];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        $path = explode($this->delimiter, $offset);

        $value = $this->data;
        foreach ($path as $index) {
            if (isset($value[$index])) {
                $value = $value[$index];
            } else {
                throw new PropertyNotFoundException();
            }
        }

        return $value;
    }


    /**
     * Will always throw an exception - properties are read-only
     *
     * @param mixed $offset
     * @param mixed $value
     * @throws ReadOnlyException
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        throw new ReadOnlyException("Cannot set predefined properties");
    }

    /**
     * Will always throw an exception - properties are read-only
     *
     * @param mixed $offset
     * @throws ReadOnlyException
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new ReadOnlyException("Cannot unset predefined properties");
    }
}
