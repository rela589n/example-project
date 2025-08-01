<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: Support/Contracts/Playground/HelloWorld/hello-world.proto

namespace App\Support\Contracts\Playground\HelloWorld\GRPC;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\GPBUtil;
use Google\Protobuf\RepeatedField;

/**
 * Generated from protobuf message <code>App.Support.Contracts.Playground.HelloWorld.GRPC.Name</code>
 */
class Name extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string firstName = 1;</code>
     */
    protected $firstName = '';
    /**
     * Generated from protobuf field <code>string lastName = 2;</code>
     */
    protected $lastName = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $firstName
     *     @type string $lastName
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Support\Contracts\Playground\HelloWorld\HelloWorld::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string firstName = 1;</code>
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Generated from protobuf field <code>string firstName = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setFirstName($var)
    {
        GPBUtil::checkString($var, True);
        $this->firstName = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string lastName = 2;</code>
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Generated from protobuf field <code>string lastName = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setLastName($var)
    {
        GPBUtil::checkString($var, True);
        $this->lastName = $var;

        return $this;
    }

}

