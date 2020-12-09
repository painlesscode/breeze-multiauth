<?php


namespace Painless\BreezeMultiAuth\Editors;


use Illuminate\Support\Str;
use Painless\BreezeMultiAuth\NodeTraverser;
use PhpParser\Node;

class AuthConfigEditor extends Editor
{
    protected function path()
    {
        return config_path('auth.php');
    }

    public function edit()
    {
        if(!array_key_exists($this->guard, config('auth.guards'))){
            $this->parseAndPut(
                function (NodeTraverser $self, Node $node) {
                    if ($node instanceof Node\Expr\Array_ && ! isset($self->attrs['found_top'])) {
                        foreach ($node->items as $item) {
                            if ($item instanceof Node\Expr\ArrayItem && $item->key->value == 'guards') {
                                array_splice($self->lines, $item->getAttribute('endLine') - 1, 0, ['        \''.$this->guard.'\' => [', '            \'driver\' => \'session\',', '            \'provider\' => \''.Str::plural($this->guard).'\',', '        ],']);
                            }

                            if ($item instanceof Node\Expr\ArrayItem && $item->key->value == 'providers') {
                                array_splice($self->lines, $item->getAttribute('endLine') + 3, 0, ['        \''.Str::plural($this->guard).'\' => [', '            \'driver\' => \'eloquent\',', '            \'model\' => App\Models\\'.Str::studly($this->guard).'::class,', '        ],']);
                            }
                            if ($item instanceof Node\Expr\ArrayItem && $item->key->value == 'passwords') {
                                array_splice($self->lines, $item->getAttribute('endLine') + 7, 0, ['        \''.Str::plural($this->guard).'\' => [', '            \'provider\' => \''.Str::plural($this->guard).'\',', '            \'table\' => \'password_resets\',', '            \'expire\' => 60,', '            \'throttle\' => 60,', '        ],']);
                            }

                        }
                        $self->attrs['found_top'] = true;
                    }
                }
            );
            return true;
        }
        return false;
    }
}
