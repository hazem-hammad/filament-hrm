<?php

// This class, InitialTokenDTO, extends an abstract DTO class and is responsible for
// representing the initial token data transfer object in the booking payment domain.
// It includes properties for booking ID and device ID, methods to convert the DTO
// to an array, map input data to the DTO properties, and retrieve the values of
// the device ID and booking ID.

namespace App\Classes\Payment\DTOs;

use App\DTOs\Common\DtoInterface;

interface InitialTokenDTOInterface extends DtoInterface {}
