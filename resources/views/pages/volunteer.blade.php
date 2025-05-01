@extends('app')

@section('body')
    <div class="relative bg-cover bg-center rounded" >
        <div class="absolute inset-0 bg-black opacity-90"></div>
        <div class="relative z-10 flex justify-center items-center h-full">
            <div class="bg-transparent px-8 py-12 w-full max-w-4xl">
                <h1 class="text-center text-white text-3xl font-bold mb-4">Volunteer with NPPC</h1>
                <p class="text-center text-white mb-6">
                    At NPPC, our volunteers are the heart and soul of our organization. By dedicating your time and skills, you help us make a significant impact in the lives of political prisoners in the United States. Whether you're looking to gain new experiences, meet like-minded individuals, or give back to society's most vulnerable population, we have a variety of volunteer opportunities that can match your interests and availability.
                </p>
                <form method="POST" id="contact-form" action="/form/volunteer" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-white text-sm mb-2">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="w-full bg-transparent border-b border-white text-white focus:outline-none focus:ring-0" required>
                        </div>
                        <div>
                            <label for="last_name" class="block text-white text-sm mb-2">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="w-full bg-transparent border-b border-white text-white focus:outline-none focus:ring-0" required>
                        </div>
                        <div>
                            <label for="email" class="block text-white text-sm mb-2">Email</label>
                            <input type="email" name="email" id="email" class="w-full bg-transparent border-b border-white text-white focus:outline-none focus:ring-0" required>
                        </div>
                        <div>
                            <label for="phone_number" class="block text-white text-sm mb-2">Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" class="w-full bg-transparent border-b border-white text-white focus:outline-none focus:ring-0">
                        </div>
                        <div>
                            <label for="state" class="block text-white text-sm mb-2">State</label>
                            <input type="text" name="state" id="state" class="w-full bg-transparent border-b border-white text-white focus:outline-none focus:ring-0">
                        </div>
                    </div>

                    <div class="my-8">
                        <h4 class="text-white text-lg font-semibold mb-4">Fields of Interests</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <input type="checkbox" name="fields_of_interest[]" id="data_entry" value="Data Entry" class="mr-2">
                                <label for="data_entry" class="text-white">Data Entry</label>
                            </div>
                            <div>
                                <input type="checkbox" name="fields_of_interest[]" id="clerical" value="Clerical" class="mr-2">
                                <label for="clerical" class="text-white">Clerical (Filing, Copying, Mailing, etc.)</label>
                            </div>
                            <div>
                                <input type="checkbox" name="fields_of_interest[]" id="event_planning" value="Event Planning" class="mr-2">
                                <label for="event_planning" class="text-white">Event planning and staffing</label>
                            </div>
                            <div>
                                <input type="checkbox" name="fields_of_interest[]" id="fundraising" value="Fundraising" class="mr-2">
                                <label for="fundraising" class="text-white">Fundraising</label>
                            </div>
                            <div>
                                <input type="checkbox" name="fields_of_interest[]" id="research" value="Research" class="mr-2">
                                <label for="research" class="text-white">Research</label>
                            </div>
                            <div>
                                <input type="checkbox" name="fields_of_interest[]" id="writing" value="Writing" class="mr-2">
                                <label for="writing" class="text-white">Writing</label>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="other_interests" class="block text-white text-sm mb-2">Other (please specify)</label>
                            <input type="text" name="other_interests" id="other_interests" class="w-full bg-transparent border-b border-white text-white focus:outline-none focus:ring-0">
                        </div>
                    </div>

                    <div class="my-8">
                        <h4 class="text-white text-lg font-semibold mb-4">Skills</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <input type="checkbox" name="skills[]" id="web_development" value="Web Development" class="mr-2">
                                <label for="web_development" class="text-white">Web Development</label>
                            </div>
                            <div>
                                <input type="checkbox" name="skills[]" id="photography" value="Photography" class="mr-2">
                                <label for="photography" class="text-white">Photography</label>
                            </div>
                            <div>
                                <input type="checkbox" name="skills[]" id="video_production" value="Video Production" class="mr-2">
                                <label for="video_production" class="text-white">Video Production</label>
                            </div>
                            <div>
                                <input type="checkbox" name="skills[]" id="accounting" value="Accounting" class="mr-2">
                                <label for="accounting" class="text-white">Accounting</label>
                            </div>
                            <div>
                                <input type="checkbox" name="skills[]" id="fundraising_skill" value="Fundraising" class="mr-2">
                                <label for="fundraising_skill" class="text-white">Fundraising</label>
                            </div>
                            <div>
                                <input type="checkbox" name="skills[]" id="legal_expertise" value="Legal Expertise" class="mr-2">
                                <label for="legal_expertise" class="text-white">Legal Expertise</label>
                            </div>
                        </div>
                    </div>

                    <div class="my-8">
                        <h4 class="text-white text-lg font-semibold mb-4">Message</h4>
                        <textarea name="message" id="message" class="w-full bg-transparent border border-white text-white p-4 focus:outline-none focus:ring-0" rows="5"></textarea>
                    </div>

                    <div class="text-center mt-12">
                        <button class="cs-btn cs-style1 g-recaptcha"
                                data-sitekey="6LdREZkqAAAAADv7Ei5dS_SZ1oVaz6A5FE7nacrw"
                                data-callback='onSubmit'
                                data-action='submit'
                                onclick="onSubmit()"

                        >
                            <span>Send inquiry</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function onSubmit(token) {
            document.getElementById("contact-form").submit();
        }
    </script>
@endsection
