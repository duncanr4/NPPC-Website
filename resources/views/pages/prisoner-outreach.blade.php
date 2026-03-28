@extends('app')

@section('body')
    <div class="py-16">
        <h1 class="text-6xl font-black text-white mb-4">Prisoner Outreach</h1>
        <div class="line"></div>

        <p class="text-white mt-8 mb-12" style="font-size: 18px; line-height: 1.5; max-width: 800px;">
            Writing letters to political prisoners is one of the most meaningful ways to show solidarity.
            Your words of support can provide comfort, connection, and hope to those who are incarcerated
            for their beliefs and activism.
        </p>

        <div class="flex flex-col-reverse md:flex-row gap-12 md:gap-16 items-start">
            {{-- Letter-writing guide --}}
            <div class="w-full md:w-1/2">
                <h3 class="text-white text-2xl font-bold mb-6">How to Write a Letter</h3>
                <ul class="space-y-4" style="list-style: none; padding: 0;">
                    <li class="flex items-start gap-3">
                        <span style="color: #5660fe; font-size: 18px; line-height: 1.6;">&#8594;</span>
                        <span class="text-white" style="font-size: 16px; line-height: 1.6;">Use the prisoner's <strong>full legal name and ID number</strong> on the envelope and letter</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span style="color: #5660fe; font-size: 18px; line-height: 1.6;">&#8594;</span>
                        <span class="text-white" style="font-size: 16px; line-height: 1.6;">Always include your <strong>return address</strong> — mail without it is often rejected</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span style="color: #5660fe; font-size: 18px; line-height: 1.6;">&#8594;</span>
                        <span class="text-white" style="font-size: 16px; line-height: 1.6;">Write on <strong>plain white paper</strong> — many facilities restrict colored or decorated stationery</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span style="color: #5660fe; font-size: 18px; line-height: 1.6;">&#8594;</span>
                        <span class="text-white" style="font-size: 16px; line-height: 1.6;"><strong>No staples, paper clips, or stickers</strong> — these will cause your letter to be rejected</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span style="color: #5660fe; font-size: 18px; line-height: 1.6;">&#8594;</span>
                        <span class="text-white" style="font-size: 16px; line-height: 1.6;">Do not include photos unless you have <strong>confirmed the facility allows them</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span style="color: #5660fe; font-size: 18px; line-height: 1.6;">&#8594;</span>
                        <span class="text-white" style="font-size: 16px; line-height: 1.6;">Keep a <strong>warm and respectful tone</strong> — introduce yourself and share why you are writing</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span style="color: #5660fe; font-size: 18px; line-height: 1.6;">&#8594;</span>
                        <span class="text-white" style="font-size: 16px; line-height: 1.6;"><strong>Ask about their interests</strong> — books, music, current events can be great conversation starters</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span style="color: #5660fe; font-size: 18px; line-height: 1.6;">&#8594;</span>
                        <span class="text-white" style="font-size: 16px; line-height: 1.6;">Send your letter via <strong>USPS</strong> — most facilities only accept mail from the postal service</span>
                    </li>
                </ul>
            </div>

            {{-- 3D Postcard --}}
            <div class="w-full md:w-1/2 flex justify-center">
                <div class="postcard-perspective" style="perspective: 1000px;">
                    <div class="postcard-card" style="width: 480px; max-width: 100%; height: 340px; border-radius: 4px; position: relative; transition: transform 0.15s ease-out, box-shadow 0.15s ease-out; box-shadow: 0 20px 60px rgba(0,0,0,0.5); cursor: default; transform-style: preserve-3d;">
                        {{-- Paper texture background --}}
                        <div style="position: absolute; inset: 0; background-color: #f5f0e8; border-radius: 4px; overflow: hidden;">
                            <div style="position: absolute; inset: 0; opacity: 0.03; background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%224%22 height=%224%22><rect width=%224%22 height=%224%22 fill=%22%23000%22 fill-opacity=%220.03%22/></svg>');"></div>
                        </div>

                        {{-- Postcard content --}}
                        <div style="position: relative; z-index: 1; display: flex; height: 100%; padding: 28px 32px;">
                            {{-- Left side: message --}}
                            <div style="flex: 1; padding-right: 24px; border-right: 1px solid #c4b9a8; display: flex; flex-direction: column;">
                                <div style="font-size: 22px; font-weight: 700; text-transform: uppercase; color: #3a3228; letter-spacing: 0.15em; margin-bottom: 16px;">Postcard</div>
                                <p style="font-size: 13px; color: #5a4f42; line-height: 1.6; margin-bottom: 16px;">
                                    Write to a political prisoner.<br>
                                    Your words of solidarity and<br>
                                    support can provide comfort.
                                </p>
                                {{-- Writing lines --}}
                                <div style="flex: 1; display: flex; flex-direction: column; justify-content: flex-end; gap: 12px; padding-bottom: 8px;">
                                    <div style="border-bottom: 1px dotted #c4b9a8; width: 100%;"></div>
                                    <div style="border-bottom: 1px dotted #c4b9a8; width: 100%;"></div>
                                    <div style="border-bottom: 1px dotted #c4b9a8; width: 100%;"></div>
                                    <div style="border-bottom: 1px dotted #c4b9a8; width: 85%;"></div>
                                </div>
                            </div>

                            {{-- Right side: address area --}}
                            <div style="flex: 0.8; padding-left: 24px; display: flex; flex-direction: column; justify-content: space-between;">
                                {{-- Stamp area --}}
                                <div style="display: flex; justify-content: flex-end;">
                                    <div style="width: 60px; height: 60px; border: 2px dashed #c4b9a8; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <span style="font-size: 10px; color: #c4b9a8; text-transform: uppercase; letter-spacing: 0.05em;">Stamp</span>
                                    </div>
                                </div>

                                {{-- To/From --}}
                                <div style="display: flex; flex-direction: column; gap: 20px;">
                                    <div>
                                        <div style="font-size: 10px; text-transform: uppercase; color: #a0937f; letter-spacing: 0.1em; margin-bottom: 6px;">From</div>
                                        <div style="border-bottom: 1px solid #c4b9a8; width: 100%; margin-bottom: 6px;"></div>
                                        <div style="border-bottom: 1px solid #c4b9a8; width: 100%;"></div>
                                    </div>
                                    <div>
                                        <div style="font-size: 10px; text-transform: uppercase; color: #a0937f; letter-spacing: 0.1em; margin-bottom: 6px;">To</div>
                                        <div style="border-bottom: 1px solid #c4b9a8; width: 100%; margin-bottom: 6px;"></div>
                                        <div style="border-bottom: 1px solid #c4b9a8; width: 100%; margin-bottom: 6px;"></div>
                                        <div style="border-bottom: 1px solid #c4b9a8; width: 80%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3D tilt effect script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const card = document.querySelector('.postcard-card');
            if (!card) return;

            const wrapper = card.closest('.postcard-perspective');
            const maxTilt = 8;

            wrapper.addEventListener('mousemove', function (e) {
                const rect = wrapper.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const rotateY = ((x - centerX) / centerX) * maxTilt;
                const rotateX = ((centerY - y) / centerY) * maxTilt;

                const shadowX = -rotateY * 2;
                const shadowY = rotateX * 2;

                card.style.transform = 'rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg)';
                card.style.boxShadow = (shadowX) + 'px ' + (20 + shadowY) + 'px 60px rgba(0,0,0,0.5)';
            });

            wrapper.addEventListener('mouseleave', function () {
                card.style.transform = 'rotateX(0deg) rotateY(0deg)';
                card.style.boxShadow = '0 20px 60px rgba(0,0,0,0.5)';
            });
        });
    </script>
@endsection
