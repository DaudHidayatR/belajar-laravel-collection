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
}
