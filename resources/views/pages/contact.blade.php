@extends('app')

@section('body')
    <div class="relative bg-cover bg-center py-24 rounded">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative z-10 flex justify-center items-center h-full">
            <div class="bg-transparent px-8 py-12 w-full max-w-lg">
                <h1 class="text-center text-white text-3xl font-bold mb-4">Contact Us</h1>
                <p class="text-center text-white mb-6">
                    Please fill out the form below to reach us. We look forward to hearing from you.
                </p>
                <form method="POST" id="contact-form" action="/form/contact" class="space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-white text-sm mb-2">Name</label>
                        <input type="text" name="name" id="name" class="w-full bg-transparent border-b border-white text-white focus:outline-none focus:ring-0" required>
                    </div>
                    <div>
                        <label for="email" class="block text-white text-sm mb-2">Email</label>
                        <input type="email" name="email" id="email" class="w-full bg-transparent border-b border-white text-white focus:outline-none focus:ring-0" required>
                    </div>
                    <div>
                        <label for="message" class="block text-white text-sm mb-2">Message</label>
                        <textarea name="message" id="message" class="w-full bg-transparent border border-white text-white p-4 focus:outline-none focus:ring-0" rows="5" required></textarea>
                    </div>
                    <div class="text-center mt-6">
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

