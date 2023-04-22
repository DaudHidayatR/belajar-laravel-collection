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
}
