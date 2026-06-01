<x-layouts.app title="Contact Us">
    <div class="bg-blue-dim dark:bg-slate-900 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl overflow-hidden grid md:grid-cols-2">
                <div class="p-8 md:p-12 bg-gradient-to-br from-primary to-orange-600 text-white">
                    <h1 class="text-3xl font-bold mb-6">Hubungi Kami</h1>
                    <div class="prose prose-invert mb-8 text-orange-100">
                        @php
                            $contactContent = App\Models\Setting::get('contact_content');
                        @endphp
                        @if($contactContent)
                            {!! $contactContent !!}
                        @else
                            <p>Memiliki pertanyaan tentang pengiriman, peninjauan mitra bestari, atau masalah teknis? Kami di sini untuk membantu.</p>
                        @endif
                    </div>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="p-2 bg-white/10 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">Email Kami</h3>
                                <p class="text-orange-100">{{ App\Models\Setting::get('contact_email', 'support@amv-openscience.id') }}</p>
                                @if(App\Models\Setting::get('contact_email_2'))
                                    <p class="text-orange-100">{{ App\Models\Setting::get('contact_email_2') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="p-2 bg-white/10 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">Kunjungi Kami</h3>
                                <p class="text-orange-100">{!! nl2br(e(App\Models\Setting::get('contact_address', "AMV Open Science HQ\nJakarta, Indonesia"))) !!}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 md:p-12">
                    <form class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama</label>
                            <input type="text" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-900 focus:ring-primary focus:border-primary transition-shadow" placeholder="Nama Anda">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                            <input type="email" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-900 focus:ring-primary focus:border-primary transition-shadow" placeholder="anda@contoh.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pesan</label>
                            <textarea rows="4" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-900 focus:ring-primary focus:border-primary transition-shadow" placeholder="Bagaimana kami bisa membantu?"></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 bg-primary hover:bg-orange-700 text-white font-bold rounded-xl shadow-lg shadow-primary/30 transition-all duration-300 transform hover:-translate-y-0.5">
                            Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
