<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class TicketShareController extends Controller
{
    private const FONT = 'C:\\Windows\\Fonts\\arial.ttf';
    private const FONT_BOLD = 'C:\\Windows\\Fonts\\arialbd.ttf';

    public function image(Ticket $ticket)
    {
        abort_unless($ticket->status === 'aprobado', 403);
        $ticket->load(['draw', 'prize', 'customer']);

        $image = imagecreatetruecolor(1200, 630);
        $this->paintBackground($image);

        $white = imagecolorallocate($image, 248, 250, 252);
        $muted = imagecolorallocate($image, 148, 163, 184);
        $cyan = imagecolorallocate($image, 103, 232, 249);
        $green = imagecolorallocate($image, 52, 211, 153);
        $greenText = imagecolorallocate($image, 209, 250, 229);
        $border = imagecolorallocate($image, 51, 65, 85);
        $card = imagecolorallocate($image, 11, 18, 40);
        $pill = imagecolorallocate($image, 6, 95, 70);

        $this->roundedRectangle($image, 55, 50, 1145, 580, 34, $card);
        imageline($image, 69, 84, 69, 546, $cyan);
        imagesetthickness($image, 10);
        imageline($image, 69, 84, 69, 546, $green);
        imagesetthickness($image, 1);
        $this->roundedRectangle($image, 900, 88, 1075, 134, 23, $pill);
        imagerectangle($image, 55, 50, 1145, 580, $border);

        $this->text($image, 'SISTEMA DE SORTEOS', 25, 110, 120, $cyan, true);
        $this->text($image, '✓ APROBADO', 19, 928, 118, $greenText, true);
        $this->text($image, 'TICKET N.º '.$ticket->ticket_number, 41, 110, 195, $white, true);
        $this->text($image, $ticket->purchase_type === 'combo' ? 'COMPRA COMBO' : 'TICKET INDIVIDUAL', 23, 110, 238, $muted, true);
        imageline($image, 110, 275, 1090, 275, $border);

        $this->text($image, 'PARTICIPANTE', 20, 110, 335, $muted);
        $this->text($image, $this->shorten($ticket->customer->full_name, 34), 31, 110, 375, $white, true);
        $this->text($image, 'PREMIO', 20, 110, 438, $muted);
        $this->text($image, $this->shorten($ticket->prize?->name ?? 'Premio', 36), 31, 110, 478, $cyan, true);

        $this->text($image, 'SORTEO', 20, 720, 335, $muted);
        $this->text($image, $this->shorten($ticket->draw->title, 26), 28, 720, 375, $white, true);
        $this->text($image, 'FECHA DEL SORTEO', 20, 720, 438, $muted);
        $date = $ticket->draw->draw_date?->format('d/m/Y') ?? 'Fecha por confirmar';
        $this->text($image, $date.' · '.$ticket->draw->draw_time, 29, 720, 478, $white, true);

        imageline($image, 110, 515, 1090, 515, $border);
        $this->text($image, 'Conserva esta imagen como constancia de tu ticket aprobado.', 18, 110, 550, $muted);

        ob_start();
        imagejpeg($image, null, 92);
        $jpeg = ob_get_clean();
        imagedestroy($image);

        return response($jpeg, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename="ticket-'.$ticket->ticket_number.'.jpg"',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function paintBackground($image): void
    {
        for ($y = 0; $y < 630; $y++) {
            $ratio = $y / 630;
            $r = (int) (15 - (2 * $ratio));
            $g = (int) (23 + (20 * $ratio));
            $b = (int) (42 + (18 * $ratio));
            imageline($image, 0, $y, 1200, $y, imagecolorallocate($image, $r, $g, $b));
        }
        imagefilledellipse($image, 1080, 85, 440, 440, imagecolorallocate($image, 17, 71, 95));
        imagefilledellipse($image, 940, 680, 520, 520, imagecolorallocate($image, 13, 83, 70));
    }

    private function roundedRectangle($image, int $left, int $top, int $right, int $bottom, int $radius, int $color): void
    {
        imagefilledrectangle($image, $left + $radius, $top, $right - $radius, $bottom, $color);
        imagefilledrectangle($image, $left, $top + $radius, $right, $bottom - $radius, $color);
        imagefilledellipse($image, $left + $radius, $top + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $right - $radius, $top + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $left + $radius, $bottom - $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $right - $radius, $bottom - $radius, $radius * 2, $radius * 2, $color);
    }

    private function text($image, string $text, int $size, int $x, int $y, int $color, bool $bold = false): void
    {
        imagettftext($image, $size, 0, $x, $y, $color, $bold ? self::FONT_BOLD : self::FONT, $text);
    }

    private function shorten(string $value, int $length): string
    {
        return mb_strimwidth($value, 0, $length, '…', 'UTF-8');
    }
}
