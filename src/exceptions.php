<?php

namespace Carrooi\Assets;

class RuntimeException extends \RuntimeException {}

class InvalidArgumentException extends \InvalidArgumentException {}

class AssetsNamespaceNotExists extends RuntimeException {}

class AssetsResourceNotExists extends RuntimeException {}

class InvalidStateException extends RuntimeException {}
