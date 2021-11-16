<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReadCsvControllerTest extends TestCase
{
    public function test_it_shows_homepage(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSeeText('Click to choose some data');
    }

    public function test_it_can_post_a_file()
    {
        Storage::fake('uploads');

        $row1 = 'Mr John Smith';
        $row2 = 'Mrs Jane Doe';
        $row3 = 'Mister Bob Smith';

        $content = implode("\n", ["homeowner", $row1, $row2, $row3]);

        $fakeCsvPath = [
            'csv_data' => UploadedFile::fake()->createWithContent('test.csv', $content),
        ];

        $response = $this->post('/', $fakeCsvPath)
            ->assertSuccessful();
    }
}
