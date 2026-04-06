<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

final class FormSubmissionController extends Controller {
    public function submit(Request $request, string $form) {
        $data = [];
        foreach ($request->all() as $k => $v) {
            if (in_array($k, ['/form/volunteer', '/form/contact', '_token', 'Token'])) {
                continue;
            }
            $data[$k] = $v;
        }

        $request->validate([
            'g-recaptcha-response' => 'required',
        ]);

        $recaptchaResponse = $request->input('g-recaptcha-response');
        $secretKey         = config('services.recaptcha.secret');

        $client   = new Client();
        $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => ['secret' => $secretKey, 'response' => $recaptchaResponse, 'remoteip' => $request->ip()],
        ]);

        $body = json_decode((string) $response->getBody());

        if (! $body->success) {
            return redirect()->back()->withErrors(['captcha' => 'ReCAPTCHA validation failed.'])->withInput();
        }

        unset($data['g-recaptcha-response']);

        // Save to database
        FormSubmission::create([
            'form_type' => $form,
            'data'      => $data,
            'status'    => 'new',
        ]);

        $formattedData = collect($data)->map(function ($value, $key) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            $formattedKey = ucwords(str_replace('_', ' ', $key));

            return "{$formattedKey}: {$value}";
        })->implode("\n");

        $subject = 'Form submission';
        if ($form === 'contact') {
            $subject = 'Contact form submission';
        }

        if ($form === 'volunteer') {
            $subject = 'Volunteer form submission';
        }

        $message = Mail::raw($formattedData, function ($message) use ($subject) {
            $message->to('info@nationalpoliticalprisonercoalition.org')
//            $message->to('test@katyusha.app')
//                ->cc(config('app.mail_cc_address'))
                ->subject($subject);
        });

        return redirect("/{$form}?form_submitted=true");
    }
}
