<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketShareImageTest extends TestCase
{
    public function test_ticket_image_fonts_are_available_inside_the_project(): void
    {
        $font = resource_path('fonts/IBMPlexSans-Regular.ttf');
        $fontBold = resource_path('fonts/IBMPlexSans-SemiBold.ttf');

        $this->assertFileIsReadable($font);
        $this->assertFileIsReadable($fontBold);

        $image = imagecreatetruecolor(300, 100);
        $text = imagettftext($image, 18, 0, 20, 50, imagecolorallocate($image, 255, 255, 255), $fontBold, 'Ticket aprobado');
        imagedestroy($image);

        $this->assertNotFalse($text);
    }
}
