<?php

namespace Carrooi\Assets;

class RuntimeException extends \RuntimeException {}

class LogicException extends \LogicException {}

class IOException extends RuntimeException {}

class PathNotFoundException extends IOException {}

class DirectoryNotFoundException extends PathNotFoundException {}

class InvalidStateException extends RuntimeException {}

class InvalidArgumentException extends \InvalidArgumentException {}

class NotImplementedException extends LogicException {}

class AssetsNamespaceNotExists extends RuntimeException {}

class AssetsResourceNotExists extends RuntimeException {}
