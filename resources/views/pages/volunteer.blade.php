@extends('app')

@section('head')
<style>
    .vol-page { max-width: 900px; margin: 0 auto; padding: 0 24px; }
    .vol-breadcrumb { font-size: 14px; color: rgba(255,255,255,0.5); margin-bottom: 8px; }
    .vol-breadcrumb a { color: #fff; text-decoration: underline; }
    .vol-title { font-size: 4rem; font-weight: 900; color: #fff; margin-bottom: 32px; line-height: 1.05; }
    .vol-intro { font-size: 18px; color: rgba(255,255,255,0.75); line-height: 1.7; margin-bottom: 24px; }
    .vol-divider { height: 1px; background: rgba(255,255,255,0.15); margin: 48px 0; }
    .vol-section-title { font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.15); padding-bottom: 12px; }
    .vol-input {
        width: 100%; background: transparent; border: 1px solid rgba(255,255,255,0.3);
        color: #fff; padding: 14px 16px; font-size: 15px;
        transition: border-color 0.2s;
    }
    .vol-input:focus { border-color: #5660fe; outline: 2px solid #5660fe; outline-offset: -2px; }
    .vol-input::placeholder { color: rgba(255,255,255,0.35); }
    .vol-textarea {
        width: 100%; background: transparent; border: 1px solid rgba(255,255,255,0.3);
        color: #fff; padding: 14px 16px; font-size: 15px; resize: vertical;
        min-height: 160px; transition: border-color 0.2s;
    }
    .vol-textarea:focus { border-color: #5660fe; outline: 2px solid #5660fe; outline-offset: -2px; }
    .vol-textarea::placeholder { color: rgba(255,255,255,0.35); }
    .vol-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .vol-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
    .vol-checkbox-list { display: flex; flex-direction: column; gap: 14px; margin-top: 8px; }
    .vol-checkbox { display: flex; align-items: center; gap: 10px; font-size: 15px; color: rgba(255,255,255,0.85); cursor: pointer; }
    .vol-checkbox input[type="checkbox"] { width: 18px; height: 18px; accent-color: #5660fe; cursor: pointer; }
    .vol-hint { font-size: 13px; color: rgba(255,255,255,0.4); margin-top: 8px; font-style: italic; }
    .vol-submit {
        background: #5660fe; color: #fff; border: none; padding: 16px 40px;
        font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
        cursor: pointer; transition: background 0.2s;
    }
    .vol-submit:hover { background: #4850e6; }
    .vol-success { background: rgba(86,96,254,0.1); border: 1px solid #5660fe; border-radius: 8px; padding: 20px; margin-bottom: 32px; color: #fff; font-size: 16px; }
    @@media (max-width: 640px) {
        .vol-title { font-size: 2.5rem; }
        .vol-grid-2, .vol-grid-3 { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('body')
<div class="vol-page" style="padding-top: 48px; padding-bottom: 80px;">

    {{-- Breadcrumb --}}
    <div class="vol-breadcrumb">
        <a href="/get-involved">Get Involved</a>
    </div>

    {{-- Title --}}
    <h1 class="vol-title">Volunteer</h1>

    {{-- Success Message --}}
    @if(request('form_submitted'))
        <div class="vol-success">
            Thank you for your interest in volunteering! We've received your application and will be in touch soon.
        </div>
    @endif

    {{-- Intro --}}
    <p class="vol-intro">
        At NPPC, our volunteers are the heart and soul of our organization. By dedicating your time and skills, you help us make a significant impact in the lives of political prisoners in the United States.
    </p>
    <p class="vol-intro">
        Whether you're looking to gain new experiences, meet like-minded individuals, or give back to society's most vulnerable population, we have a variety of volunteer opportunities that can match your interests and availability. We hope you will fill out the application below.
    </p>

    <div class="vol-divider"></div>

    {{-- Form --}}
    <form method="POST" id="volunteer-form" action="/form/volunteer">
        @csrf

        {{-- Contact Information --}}
        <h2 class="vol-section-title">Contact Information</h2>

        <div class="vol-grid-2" style="margin-bottom: 16px;">
            <input type="text" name="first_name" class="vol-input" placeholder="First Name" required>
            <input type="text" name="last_name" class="vol-input" placeholder="Last Name" required>
        </div>

        <div style="margin-bottom: 16px;">
            <input type="email" name="email" class="vol-input" placeholder="Email" required>
        </div>

        <div class="vol-grid-3" style="margin-bottom: 16px;">
            <input type="text" name="city" class="vol-input" placeholder="City">
            <input type="text" name="state" class="vol-input" placeholder="State">
            <input type="text" name="zip_code" class="vol-input" placeholder="Zip Code">
        </div>

        <div class="vol-grid-2" style="margin-bottom: 16px;">
            <input type="text" name="phone_number" class="vol-input" placeholder="Phone Number">
            <input type="text" name="mobile_phone" class="vol-input" placeholder="Mobile Phone">
        </div>

        <div class="vol-divider"></div>

        {{-- Skills & Interests --}}
        <h2 class="vol-section-title">Skills & Interests</h2>

        <div class="vol-checkbox-list">
            <label class="vol-checkbox">
                <input type="checkbox" name="fields_of_interest[]" value="Data Entry"> Data Entry
            </label>
            <label class="vol-checkbox">
                <input type="checkbox" name="fields_of_interest[]" value="Clerical"> Clerical (Filing, Copying, Mailing, etc.)
            </label>
            <label class="vol-checkbox">
                <input type="checkbox" name="fields_of_interest[]" value="Event Planning"> Event planning and staffing
            </label>
            <label class="vol-checkbox">
                <input type="checkbox" name="fields_of_interest[]" value="Fundraising"> Fundraising
            </label>
            <label class="vol-checkbox">
                <input type="checkbox" name="fields_of_interest[]" value="Research"> Research
            </label>
            <label class="vol-checkbox">
                <input type="checkbox" name="fields_of_interest[]" value="Writing"> Writing
            </label>
            <label class="vol-checkbox">
                <input type="checkbox" name="fields_of_interest[]" value="Other"> Other (please specify)
            </label>
        </div>
        <div class="vol-hint">Please check all areas where you have experience and interest in volunteering</div>

        <div style="margin-top: 24px;">
            <input type="text" name="other_interests" class="vol-input" placeholder="Other interests (if applicable)">
        </div>

        <div class="vol-divider"></div>

        {{-- Skills --}}
        <h2 class="vol-section-title">Skills</h2>

        <div class="vol-checkbox-list">
            <label class="vol-checkbox">
                <input type="checkbox" name="skills[]" value="Web Development"> Web Development
            </label>
            <label class="vol-checkbox">
                <input type="checkbox" name="skills[]" value="Photography"> Photography
            </label>
            <label class="vol-checkbox">
                <input type="checkbox" name="skills[]" value="Video Production"> Video Production
            </label>
            <label class="vol-checkbox">
                <input type="checkbox" name="skills[]" value="Accounting"> Accounting
            </label>
            <label class="vol-checkbox">
                <input type="checkbox" name="skills[]" value="Fundraising"> Fundraising
            </label>
            <label class="vol-checkbox">
                <input type="checkbox" name="skills[]" value="Legal Expertise"> Legal Expertise
            </label>
        </div>

        <div class="vol-divider"></div>

        {{-- Educational Background --}}
        <h2 class="vol-section-title">Educational Background</h2>
        <textarea name="educational_background" class="vol-textarea" placeholder="Enter your message"></textarea>

        <div class="vol-divider"></div>

        {{-- Work Experience --}}
        <h2 class="vol-section-title">Work Experience</h2>
        <textarea name="work_experience" class="vol-textarea" placeholder="Enter your message"></textarea>

        <div class="vol-divider"></div>

        {{-- Why Volunteer --}}
        <h2 class="vol-section-title">Why do you want to volunteer at NPPC?</h2>
        <textarea name="message" class="vol-textarea" placeholder="Enter your message"></textarea>
        <div class="vol-hint">Please provide a brief description of your interest in the work of the National Political Prisoner Coalition</div>

        <div style="margin-top: 48px;">
            <button type="submit" class="vol-submit">Submit</button>
        </div>
    </form>
</div>
@endsection
