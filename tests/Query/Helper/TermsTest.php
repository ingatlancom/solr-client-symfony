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

    /**
     * @test
     * @dataProvider provideValidTerms
     */
    public function it_creates_terms_query_string(\Closure $terms, string $expectedQuery): void
    {
        $this->assertSame($expectedQuery, $terms()->toString());
    }

    public static function provideValidTerms(): iterable
    {
        // we need to use closures to ensure the code actually runs in the test's context so coverage can be generated
        yield 'default-separator' => [
            static function (): Terms {
                return Terms::create('id', [1, 2, 3]);
            },
            '{!terms f=id}1,2,3',
        ];

        yield 'method' => [
            static function (): Terms {
                return Terms::create('id', [1, 2, 3])->method('termsFilter');
            },
            '{!terms f=id method=termsFilter}1,2,3',
        ];

        yield 'separator-space' => [
            static function (): Terms {
                return Terms::create('id', [1, 2, 3])->separator(' ');
            },
            '{!terms f=id separator=" "}1 2 3',
        ];

        yield 'separator-double-quote' => [
            static function (): Terms {
                return Terms::create('id', [1, 2, 3])->separator('"');
            },
            '{!terms f=id separator="\""}1"2"3',
        ];

        yield 'separator-single-quote' => [
            static function (): Terms {
                return Terms::create('id', [1, 2, 3])->separator("'");
            },
            sprintf('{!terms f=id separator="\%s"}', "'")."1'2'3",
        ];

        yield 'separator-with-backslash' => [
            static function (): Terms {
                return Terms::create('id', [1, 2, 3])->separator('\~');
            },
            sprintf('{!terms f=id separator="\%s"}1\~2\~3', "\~"),
        ];

        yield 'different-values' => [
            static function (): Terms {
                return Terms::create('id', ['doc1', 'doc2', 'doc3']);
            },
            '{!terms f=id}doc1,doc2,doc3',
        ];

        yield 'with-cache-true' => [
            static function (): Terms {
                return Terms::create('id', [1, 2, 3])->cache(true);
            },
            '{!terms f=id cache=true}1,2,3',
        ];

        yield 'with-cache-false' => [
            static function (): Terms {
                return Terms::create('id', [1, 2, 3])->cache(false);
            },
            '{!terms f=id cache=false}1,2,3',
        ];

        yield 'all-parts' => [
            static function (): Terms {
                return Terms::create('other_id', [1, 2, 3])->method('termsFilter')->separator(' ')->cache(false);
            },
            '{!terms f=other_id method=termsFilter separator=" " cache=false}1 2 3',
        ];
    }
}
