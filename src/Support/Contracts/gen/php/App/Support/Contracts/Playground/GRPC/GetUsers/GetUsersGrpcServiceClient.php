<?php
// GENERATED CODE -- DO NOT EDIT!

namespace App\Support\Contracts\Playground\GRPC\GetUsers;

/**
 * Internal gRPC service for user-related operations.
 */
class GetUsersGrpcServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * Returns a list of users filtered by an optional USN number.
     * @param \App\Support\Contracts\Playground\GRPC\GetUsers\GetUsersRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\App\Support\Contracts\Playground\GRPC\GetUsers\GetUsersResponse>
     */
    public function GetUsers(\App\Support\Contracts\Playground\GRPC\GetUsers\GetUsersRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/get_users.v1.GetUsersGrpcService/GetUsers',
        $argument,
        ['\App\Support\Contracts\Playground\GRPC\GetUsers\GetUsersResponse', 'decode'],
        $metadata, $options);
    }

}
