<?php
// GENERATED CODE -- DO NOT EDIT!

namespace App\Support\Contracts\Playground\GRPC\HelloWorld;

/**
 */
class HelloWorldGrpcServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \App\Support\Contracts\Playground\GRPC\HelloWorld\GreetRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\App\Support\Contracts\Playground\GRPC\HelloWorld\GreetResponse>
     */
    public function Greet(\App\Support\Contracts\Playground\GRPC\HelloWorld\GreetRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/hello_world.v1.HelloWorldGrpcService/Greet',
        $argument,
        ['\App\Support\Contracts\Playground\GRPC\HelloWorld\GreetResponse', 'decode'],
        $metadata, $options);
    }

}
