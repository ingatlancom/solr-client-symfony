<?php

declare(strict_types=1);

/*
 * This file is part of Solr Client Symfony package.
 *
 * (c) ingatlan.com Zrt. <fejlesztes@ingatlan.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace iCom\SolrClient\Tests\Query\Helper;

use iCom\SolrClient\Query\Helper\Terms;
use PHPUnit\Framework\TestCase;

/**
 * @covers \iCom\SolrClient\Query\Helper\Terms
 */
final class TermsTest extends TestCase
{
    /** @test */
    public function it_throws_for_empty_field(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "field" parameter can not be empty.');

        Terms::create('', [1]);
    }

    /** @test */
    public function it_throws_for_empty_values(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "values" parameter can not be empty.');

        Terms::create('id', []);
    }

    /** @test */
    public function it_throws_for_invalid_methods(): void
    {
        $t = Terms::create('id', [1]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Available methods are: "termsFilter", "booleanQuery", "automaton", "docValuesTermsFilter"');

        $t->method('invalid');
    }

    /** @test */
    public function it_has_a_method_option(): void
    {
        $terms = Terms::create('id', [1, 2, 3]);
        $new = $terms->method('termsFilter');

        self::assertNotSame($terms, $new);
        self::assertSame('{!terms f=id method=termsFilter}1,2,3', $new->toString());
    }

    /** @test */
    public function it_has_a_cache_option(): void
    {
        $terms = Terms::create('id', [1, 2, 3]);
        $new = $terms->cache(true);

        self::assertNotSame($terms, $new);
        self::assertSame('{!terms f=id cache=true}1,2,3', $new->toString());

        $new = $terms->cache(false);

        self::assertSame('{!terms f=id cache=false}1,2,3', $new->toString());
    }

    /** @test */
    public function it_merges_its_all_options_together(): void
    {
        $terms = Terms::create('other_id', [1, 2, 3])->method('termsFilter')->separator(' ')->cache(false);

        self::assertSame('{!terms f=other_id method=termsFilter separator=" " cache=false}1 2 3', $terms->toString());
    }

    /** @test */
    public function its_default_term_separator_is_a_comma(): void
    {
        $terms = Terms::create('id', [1, 2, 3]);

        self::assertSame('{!terms f=id}1,2,3', $terms->toString());
    }

    /** @test */
    public function its_term_separator_is_configurable(): void
    {
        $terms = Terms::create('id', [1, 2, 3]);
        $new = $terms->separator(' ');

        self::assertNotSame($terms, $new);
        self::assertSame('{!terms f=id separator=" "}1 2 3', $new->toString());
    }

    /**
     * @param array<int> $values
     *
     * @test
     * @dataProvider escapes
     */
    public function it_escapes_the_term_separator_correctly(array $values, string $separator, string $expected): void
    {
        $terms = Terms::create('id', $values)->separator($separator);

        self::assertSame($expected, $terms->toString());
    }

    /**
     * @return iterable<string, array>
     */
    public function escapes(): iterable
    {
        yield 'double quote' => [[1, 2, 3], '"', '{!terms f=id separator="\""}1"2"3'];
        yield 'single quote' => [[1, 2, 3], "'", sprintf('{!terms f=id separator="\%s"}', "'")."1'2'3"];
        yield 'with backslash' => [[1, 2, 3], '\~', sprintf('{!terms f=id separator="\%s"}1\~2\~3', "\~")];
    }
}
