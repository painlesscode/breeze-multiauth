<?php


namespace Painless\BreezeMultiAuth\Editors;


use Painless\BreezeMultiAuth\NodeTraverser;
use PhpParser\Node;

class RedirectIfAuthMiddlewareEditor extends Editor
{
    public function path()
    {
        return app_path('Http/Middleware/RedirectIfAuthenticated.php');
    }

    public function edit(){
        $this->parseAndPut(
            function (NodeTraverser $self, Node $node) {
                if($node instanceof Node\Stmt\Class_ ){
                    $self->attrs['found_class'] = true;
                }
                if($node instanceof Node\Stmt\ClassMethod && ! isset($self->attrs['found_method']) && isset($self->attrs['found_class'])) {
                    if($node->name === 'handle') $self->found_class = true;
                    array_splice($self->lines, $node->getAttribute('startLine')+1, 0, ['        if(in_array(\''.$this->guard.'\', $guards, true) && Auth::guard(\''.$this->guard.'\')->check()) {','            return redirect(route(\''.$this->guard.'.dashboard\'));','        }']);
                }
            }
        );
    }
}
