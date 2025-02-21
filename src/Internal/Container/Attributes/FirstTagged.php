<?php
namespace Swark\Internal\Container\Attributes;

use Attribute;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;

/**
 * Finds the <strong>first</strong> instance from the dependency container, tagged with the given tag.
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
final class FirstTagged implements ContextualAttribute
{
    public function __construct(
        public string $tag,
    ) {
    }

    /**
     * Resolve the tag.
     *
     * @param  self  $attribute
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return mixed
     */
    public static function resolve(self $attribute, Container $container)
    {
        /** @var iterable $r */
        $r = $container->tagged($attribute->tag);

        throw_if($r->count() == 0, sprintf("Unable to resolve %s: tag not defined", $attribute->tag));

        return $r->getIterator()->current();
    }
}
