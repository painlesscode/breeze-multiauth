<?php


namespace Painless\BreezeMultiAuth\Editors;


use Painless\BreezeMultiAuth\NodeTraverser;
use PhpParser\Node;

class AuthenticateMiddlewareEditor extends Editor
{
    public function path()
    {
        return app_path('Http/Middleware/Authenticate.php');
    }

    public function edit(){
        $this->parseAndPut(
            function (NodeTraverser $self, Node $node) {
                if($node instanceof Node\Stmt\Class_ ){
                    $self->attrs['found_class'] = true;
                }
                if($node instanceof Node\Stmt\ClassMethod && ! isset($self->attrs['found_method']) && isset($self->attrs['found_class'])) {
                    if($node->name === 'redirectTo') $self->found_class = true;
                    array_splice($self->lines, $node->getAttribute('startLine')+1, 0, ['        if(! $request->expectsJson() && $request->is(\''.$this->guard.'*\')) {','            return route(\''.$this->guard.'.login\');','        }']);
                }
            }
        );
    }
}
