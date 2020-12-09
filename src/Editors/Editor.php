<?php


namespace Painless\BreezeMultiAuth\Editors;


use Illuminate\Filesystem\Filesystem;
use Painless\BreezeMultiAuth\NodeTraverser;
use PhpParser\Error;
use PhpParser\ParserFactory;

class Editor
{
    protected $guard;

    public function __construct($guard)
    {
        $this->guard = $guard;
    }

    protected function parseAndPut($handler){
        $content = (new Filesystem)->get($this->path());
        $lines = explode("\n", $content);
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($content);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return;
        }
        $traverser = new \PhpParser\NodeTraverser();
        $my_traverser = new NodeTraverser(
            $lines,
            $handler
        );
        $traverser->addVisitor($my_traverser);
        $traverser->traverse($ast);
        (new Filesystem)->put($this->path(), implode("\n", $my_traverser->getLines()));
    }

    protected function path(){
        throw new \ErrorException('Editor path not found');
    }
}
