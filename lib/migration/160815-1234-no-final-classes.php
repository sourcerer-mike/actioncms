<?php

/**
 * Removed final classes from WordPress.
 *
 * One of the most annoying philosophies of WordPress is,
 * that they want the API classes to be final.
 * This makes you go wild while developing
 * and you won't be able to extend WordPress properly to your own benefits or to the customer needs.
 *
 * ## How are those final classes in WordPress a problem?
 *
 * I understand why they want final classes in WordPress.
 * On a WordCamp a core developer told me,
 * that they do not want the "common developer" to extend some classes of WordPress.
 * They shall just be used as they are and nothing else.
 * Because one day they might change and due to the lacking knowledge of some people
 * their application will break.
 * That's what he said.
 *
 * This might be true for the "average" rookie developer.
 * But some of us no longer live in that rookie world
 * and want to move things, make it bigger and better.
 * We know when and why the application break,
 * so most of us are able to fix the problem.
 * So in my opinion the final classes (and final functions later on)
 * should be replaced by proper extensible non-final classes.
 *
 * ## What is better than final classes?
 *
 * Having normal classes instead of final classes is a quick and easy solution.
 * Imagine your own post type which can just be a class like this:
 *
 * ```
 * class Books extends WP_Post_Type {
 * }
 * ```
 *
 * Now you're good.
 * You should even be allowed to register a post type just by using the class.
 * All hooks, filter and actions could be applied to your own class.
 *
 * To sum it up: There is always need to extend some classes that WordPress brings.
 * Unfortunately you can't due to the limitations that "final class" brings.
 *
 * ## Why is it better to not have final classes?
 *
 * Imagine once again the own post type:
 *
 * ```
 * class Movie extends WP_Post_Type {
 * }
 * ```
 *
 * By removing the `final` from all classes the above is now possible.
 * All hooks, filter and actions can now be applied to your own class.
 * This makes all logic very distinguishable.
 * Better maintainability as a fundamental software quality is alive again,
 * because you know just the place where to look things up.
 *
 * We as developers should not be powdered all the time and kept safe no matter what.
 * Experience is the sum of all failures so lets build a systems that challenges you / works as expected.
 *
 * This is one step further to proper OOP.
 * Just like mostly other Frameworks in the world work like.
 * With some semantic versions and a good class loader everything will be fine.
 */
class NoFinalClass {
	public function Parse_Stmt_Class( \PhpParser\Node\Stmt\Class_ $node ) {
		if ( ! $node->isFinal() ) {
			// not final => ignore
			return $node;
		}

		$node->type ^= $node::MODIFIER_FINAL;

		assert( ! $node->isFinal() );

		return $node;
	}

	/**
	 * @param \PhpParser\Node[] $nodes
	 *
	 * @return mixed
	 */
	public function parse( $nodes ) {
		foreach ( $nodes as $pos => $node ) {
			if ( ! is_object( $node ) ) {
				continue;
			}

			$methodName = 'Parse_' . $node->getType();

			if ( ! method_exists( $this, $methodName ) ) {
				continue;
			}

			$nodes[ $pos ] = $this->$methodName( $node );
		}

		return $nodes;
	}
}

return array( new NoFinalClass(), "parse" );