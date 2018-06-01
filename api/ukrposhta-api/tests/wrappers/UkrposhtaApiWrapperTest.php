<?php
/**
 * Created by PhpStorm.
 * User: eugene
 * Date: 04/04/18
 * Time: 14:53
 */

require_once '../../wrappers/UkrposhtaApiWrapper.php';
require_once '../../kernel/UkrposhtaApi.php';

class UkrposhtaApiWrapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var UkrposhtaApiWrapper
     */
    private $wrapper;

    protected function setUp()
    {
        $this->wrapper = new UkrposhtaApiWrapper(
            'f9027fbb-cf33-3e11-84bb-5484491e2c94',
            'ba5378df-985e-49c5-9cf3-d222fa60aa68');
    }
}
