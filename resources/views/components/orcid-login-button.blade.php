<div class="mt-4 flex flex-col gap-2">
    <div class="relative flex items-center">
        <div class="flex-grow border-t border-gray-300 dark:border-gray-700"></div>
        <span class="flex-shrink mx-4 text-gray-400 text-sm">Or</span>
        <div class="flex-grow border-t border-gray-300 dark:border-gray-700"></div>
    </div>
    
    <a href="{{ route('orcid.redirect') }}" 
       class="flex items-center justify-center gap-2 w-full px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
        <img src="https://orcid.org/assets/vectors/orcid.logo.icon.svg" alt="ORCID" class="w-5 h-5">
        Login with ORCID
    </a>
</div>
