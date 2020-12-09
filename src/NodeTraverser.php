<?php


namespace Painless\BreezeMultiAuth;


use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class NodeTraverser extends NodeVisitorAbstract
{
    public $attrs = [];
    public $lines = [];
    protected $handler;
    public function __construct($lines, $handler)
    {
        $this->lines = $lines;
        $this->handler = $handler;
    }
    public function enterNode(Node $node) {
        call_user_func($this->handler, $this, $node);
    }

    public function getLines(){
        return $this->lines;
    }
}
