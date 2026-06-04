@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800 text-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Compose Message</h1>
            <p class="text-gray-400">Send a new message</p>
        </div>

        <!-- Compose Message Form -->
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700/50 shadow-xl overflow-hidden">
            <div class="p-6">
                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    
                    <!-- To -->
                    <div class="mb-5">
                        <label for="to" class="block text-sm font-medium text-gray-300 mb-2">To</label>
                        <input type="email" name="to" id="to" 
                            class="w-full px-4 py-2.5 bg-gray-900/70 border border-gray-700/50 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-transparent transition-all duration-200" 
                            placeholder="recipient@example.com"
                            required>
                    </div>
                    
                    <!-- Subject -->
                    <div class="mb-5">
                        <label for="subject" class="block text-sm font-medium text-gray-300 mb-2">Subject</label>
                        <input type="text" name="subject" id="subject" 
                            class="w-full px-4 py-2.5 bg-gray-900/70 border border-gray-700/50 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-transparent transition-all duration-200" 
                            placeholder="Enter subject"
                            required>
                    </div>
                    
                    <!-- Message -->
                    <div class="mb-5">
                        <label for="message" class="block text-sm font-medium text-gray-300 mb-2">Message</label>
                        <textarea name="message" id="message" rows="6" 
                            class="w-full px-4 py-2.5 bg-gray-900/70 border border-gray-700/50 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-transparent transition-all duration-200 resize-none" 
                            placeholder="Type your message here..."
                            required></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" 
                            class="bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2.5 px-6 rounded-lg transition-all duration-200 shadow-lg shadow-cyan-900/20 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
