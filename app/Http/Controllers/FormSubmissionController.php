<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

final class FormSubmissionController extends Controller {
    public function submit(Request $request, string $form) {
        // reCAPTCHA validation (only if secret is configured)
        $secretKey = config('services.recaptcha.secret');
        if ($secretKey) {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            if ($recaptchaResponse) {
                $client   = new Client();
                $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                    'form_params' => ['secret' => $secretKey, 'response' => $recaptchaResponse, 'remoteip' => $request->ip()],
                ]);

                $body = json_decode((string) $response->getBody());

                if (! $body->success) {
                    return redirect()->back()->withErrors(['captcha' => 'ReCAPTCHA validation failed.'])->withInput();
                }
            }
        }

        $data = [];
        foreach ($request->all() as $k => $v) {
            if (in_array($k, ['/form/volunteer', '/form/contact', '_token', 'Token', 'g-recaptcha-response'])) {
                continue;
            }
            $data[$k] = $v;
        }

        FormSubmission::create([
            'form_type' => $form,
            'data'      => $data,
            'status'    => 'new',
        ]);

        // Send email notification
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

        try {
            Mail::raw($formattedData, function ($message) use ($subject) {
                $message->to('info@nationalpoliticalprisonercoalition.org')
                    ->subject($subject);
            });
        } catch (\Exception $e) {
            // Email may fail if mail isn't configured — submission is already saved
        }

        return redirect("/{$form}?form_submitted=true");
    }
}
