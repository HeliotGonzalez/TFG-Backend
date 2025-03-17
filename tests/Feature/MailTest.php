<?php

namespace Tests\Feature;

use App\Mail\OtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MailTest extends TestCase
{
    public function testOtpMailRender()
    {
        // Crea un usuario de prueba con datos ficticios.
        $user = new User([
            'name'            => 'Usuario Test',
            'email'           => 'test@example.com',
            'otp_code'        => '123456',
            'otp_expires_at'  => Carbon::now()->addMinutes(15),
        ]);

        // Crea una instancia del mailable.
        $mailable = new OtpMail($user);

        // Verifica que el asunto del correo es el esperado.
        $this->assertEquals('Código de verificación', $mailable->envelope()->subject);

        // Renderiza el contenido del correo.
        $rendered = $mailable->render();

        // Realiza aserciones para asegurarte de que el contenido contiene el OTP y datos del usuario.
        $this->assertStringContainsString('123456', $rendered);
        $this->assertStringContainsString($user->name, $rendered);
    }
}
