<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function PHPUnit\Framework\assertEqualsCanonicalizing;

class CollectionTest extends TestCase
{
    public function testCreateCollection(): void
    {
        $collection = collect([1,2,3]);
        $this->assertEqualsCanonicalizing([1,2,3], $collection->all());
    }
    public function testForEach()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        foreach ($collection as $key => $value)
        {
        self::assertEquals($key+1, $value);
        }

    }
    public function testCrud()
    {
        $collection = collect([]);
        $collection->push(1,2,3);
        assertEqualsCanonicalizing([1,2,3], $collection->all());

        $result = $collection->pop();
        assertEqualsCanonicalizing([1,2], $collection->all());
    }
    public function testMap()
    {
        $collection = collect([1,2,3]);
        $result = $collection->map(function ($item){
            return $item * 2;
        });
        assertEqualsCanonicalizing([2,4,6],$result->all());
    }
    public function testMapInto()
    {
        $collection = collect(['daud']);
        $result = $collection->mapInto(Person::class);
        $this->assertEqualsCanonicalizing([new Person("daud")], $result->all());
    }
    public function testMapSpread()
    {
        $collection = collect([['Daud','Hidayat'], ['Bintang', 'Rahmatullah']]);
        $result = $collection->mapSpread(function ($firstName, $lastName){
            $fullname = $firstName." ".$lastName;
            return new Person($fullname);
        });
        self::assertEquals([
            new Person("Daud Hidayat"),
            new Person('Bintang Rahmatullah')
        ], $result->all());
    }
    public function testMapGroups()
    {
        $collection = collect([
            [
                'name' => 'daud',
                'department' => 'IT'
            ],
            [
                'name' => 'Hidayat',
                'department' => 'IT'
            ],
            [
                'name' => 'Ramadhan',
                'department' => 'HR'
            ]
        ]);
        $result = $collection->mapToGroups(function ($item){
            return[$item['department']=> $item['name']];
        });
        self::assertEquals([
            'IT' => collect(['daud', 'Hidayat']),
            'HR' => collect(['Ramadhan'])
        ], $result->all());
    }
    public function testZip()
    {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4,5,6]);

        $collection3 = $collection1->zip($collection2);

        self::assertEquals([
            collect([1,4]),
            collect([2,5]),
            collect([3,6])
        ], $collection3->all());
    }
    public function testConcat()
    {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4,5,6]);

        $collection3 = $collection1->concat($collection2);

        self::assertEquals([1,2,3,4,5,6], $collection3->all());
    }
    public function testCombine()
    {
        $collection1 = collect(['name', 'country']);
        $collection2 = collect(['Daud', 'Indonesia']);
        $collection3 = $collection1->combine($collection2);

        $this->assertEqualsCanonicalizing([
            'name' => 'Daud',
            'country'=>'Indonesia'
        ],$collection3->all());
    }

    public function testCollepse()
    {
        $collection = collect([
            [1,2,3],
            [4,5,6],
            [7,8,9]
        ]);
        $result = $collection->collapse();
        self::assertEqualsCanonicalizing([1,2,3,4,5,6,7,8,9], $result->all());
    }
    public function testFlatMap()
    {
        $collection = collect([
            [
            'name' => 'Daud',
            'hobbies' => ['Coding', 'Gaming']
            ],
            [
                'name' => 'Hidayat',
                'hobbies' => ['Reading', 'Writing']
            ]
        ]);
        $result = $collection->flatMap(function ($item){
            $hobbies = $item['hobbies'];
            return $hobbies;
        });

        self::assertEqualsCanonicalizing(['Coding', 'Gaming','Reading', 'Writing'], $result->all());
    }
    public function testjoin()
    {
        $collection = collect(['Daud', 'Hidayat', 'Ramadhan']);

        self::assertEquals("Daud-Hidayat-Ramadhan", $collection->join("-"));
        self::assertEquals("Daud-Hidayat_Ramadhan", $collection->join("-", "_"));
    }
    public function testFilter()
    {
        $collection = collect([
            'daud' => 100,
            'bintang' => 80,
            'siraj' => 90
        ]);

        $result = $collection->filter(function ($value, $key){
            return $value >= 90;
        });
        self::assertEquals([
            'daud' => 100,
            'siraj' => 90
        ], $result->all());
    }
    public function testFilterIndex()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);
        $result = $collection->filter(function ($value, $key){
            return $value % 2 == 0;
        });
        self::assertEqualsCanonicalizing([2,4,6,8,10], $result->all());
    }
    public function testPartition()
    {
        $collection = collect([
            'daud' => 100,
            'bintang' => 80,
            'siraj' => 90
        ]);

        [$result1, $result2] = $collection->partition(function ($value, $key){
            return $value >= 90;
        });
        self::assertEquals([
            'daud' => 100,
            'siraj' => 90
        ], $result1->all());
        self::assertEquals([
            'bintang' => 80
        ], $result2->all());
    }
    public function testTesting()
    {
        $collection = collect(['daud', 'hidayat', 'ramadhan']);
        self::assertTrue($collection->contains('daud'));
        self::assertTrue($collection->contains(function ($value, $key){
            return $value == 'daud';
        }));
    }
    public function testGruping()
    {
        $collection = collect([
            [
                'name' => 'daud',
                'department' => 'IT'
            ],
            [
                'name' => 'Hidayat',
                'department' => 'IT'
            ],
            [
                'name' => 'Ramadhan',
                'department' => 'HR'
            ]
        ]);
        $result = $collection->groupBy('department');

        self::assertEquals([
            "IT" => collect([
                [
                    'name' => 'daud',
                    'department' => 'IT'
                ],
                [
                    'name' => 'Hidayat',
                    'department' => 'IT'
                ]
            ]),
            "HR" => collect([
                [
                    'name' => 'Ramadhan',
                    'department' => 'HR'
                ]
            ])
        ], $result->all());
        $result = $collection->groupBy(function ($value, $key){
            return $value['department'];
        });
        self::assertEquals([
            "IT" => collect([
                [
                    'name' => 'daud',
                    'department' => 'IT'
                ],
                [
                    'name' => 'Hidayat',
                    'department' => 'IT'
                ]
            ]),
            "HR" => collect([
                [
                    'name' => 'Ramadhan',
                    'department' => 'HR'
                ]
            ])
        ], $result->all());
    }
    public function testSlice()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);
        $result = $collection->slice(3);

        self::assertEqualsCanonicalizing([4,5,6,7,8,9,10], $result->all());

        $result = $collection->slice(3,2);
        self::assertEqualsCanonicalizing([4,5], $result->all());
    }
    public function testTake()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);

        $result = $collection->take(3);
        self::assertEqualsCanonicalizing([1,2,3], $result->all());

        $result = $collection->takeUntil(function ($value, $key){
            return $value == 3;
        });
        self::assertEqualsCanonicalizing([1,2], $result->all());

        $result = $collection->takeWhile(function ($value, $key){
            return $value < 3;
        });
        self::assertEqualsCanonicalizing([1,2], $result->all());
    }
    public function testSkip()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);

        $result = $collection->skip(3);
        self::assertEqualsCanonicalizing([4,5,6,7,8,9], $result->all());

        $result = $collection->skipUntil(function ($value, $key){
            return $value == 3;
        });
        self::assertEqualsCanonicalizing([3,4,5,6,7,8,9], $result->all());

        $result = $collection->skipWhile(function ($value, $key){
            return $value < 3;
        });
        self::assertEqualsCanonicalizing([3,4,5,6,7,8,9], $result->all());
    }
    public function testChunked()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->chunk(3);

        assertEqualsCanonicalizing([1,2,3], $result->all()[0]->all());
        assertEqualsCanonicalizing([4,5,6], $result->all()[1]->all());
        assertEqualsCanonicalizing([7,8,9], $result->all()[2]->all());
    }
    public function testFirst()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->first();
        self::assertEquals(1,$result);

        $result = $collection->first(function ($value, $key){
           return $value > 3;
        });
        self::assertEquals(4, $result);

    }
    public function testLast()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->last();
        self::assertEquals(9,$result);

        $result = $collection->last(function ($value, $key){
            return $value < 3;
        });
        self::assertEquals(2, $result);
    }
    public function testRandom()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->random();
        self::assertTrue(in_array($result,[1,2,3,4,5,6,7,8,9]));
    }
    public function testCheckingExistence()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);

        self::assertTrue($collection->isNotEmpty());
        self::assertNotTrue($collection->isEmpty());
        self::assertTrue($collection->contains(5));
        self::assertTrue($collection->contains(function ($value,$key){
            return $value > 8;
        }));
    }
    public function testOrdering()
    {
        $collection = collect([1,2,3,4,8,9,7,5,6]);
        $result = $collection->sort();
        assertEqualsCanonicalizing([1,2,3,4,5,6,7,8,9], $result->all());

        $result = $collection->sortDesc();
        assertEqualsCanonicalizing([9,8,7,6,5,4,3,2,1], $result->all());
    }
    public function testAggregate()
    {
        $collection = collect([1,2,3,4,5,7,8,6,9]);
        $result= $collection->sum();
        self::assertEquals(45, $result);

        $result = $collection->avg();
        self::assertEquals(5, $result);

        $result= $collection->max();
        self::assertEquals(9, $result);

        $result= $collection->min();
        self::assertEquals(1, $result);
    }
    public function testReduce()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->reduce(function ($carry, $item){
            return $carry + $item;
        });
        self::assertEquals(45, $result);
    }
}
