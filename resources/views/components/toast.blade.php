<div
    x-data="{ 
        notifications: [],
        add(e) {
            let id = Date.now();
            this.notifications.push({
                id: id,
                type: e.detail.type || 'info',
                message: e.detail.message,
                show: false
            });
            this.$nextTick(() => {
                let index = this.notifications.findIndex(n => n.id === id);
                this.notifications[index].show = true;
            });
            setTimeout(() => {
                this.remove(id);
            }, 5000);
        },
        remove(id) {
            let index = this.notifications.findIndex(n => n.id === id);
            if (index !== -1) {
                this.notifications[index].show = false;
                setTimeout(() => {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                }, 300);
            }
        }
    }"
    @notify.window="add($event)"
    class="fixed bottom-8 right-8 z-[100] flex flex-col gap-3 items-end pointer-events-none"
>
    <template x-for="n in notifications" :key="n.id">
        <div
            x-show="n.show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-4 opacity-0 scale-95"
            x-transition:enter-end="translate-y-0 opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 opacity-100 scale-100"
            x-transition:leave-end="translate-y-2 opacity-0"
            class="pointer-events-auto bg-navy-950/90 backdrop-blur-xl border border-white/10 px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 min-w-[320px] max-w-md"
        >
            <div :class="{
                'bg-primary-500': n.type === 'info' || n.type === 'success',
                'bg-amber-500': n.type === 'warning',
                'bg-red-500': n.type === 'danger' || n.type === 'error'
            }" class="w-2 h-2 rounded-full animate-pulse shadow-lg"></div>
            
            <div class="flex-grow">
                <p class="text-[10px] font-black uppercase tracking-[2px] text-white/40 mb-1" x-text="n.type === 'success' ? 'Protocol Success' : 'System Intel'"></p>
                <p class="text-xs font-bold text-white tracking-tight" x-text="n.message"></p>
            </div>

            <button @click="remove(n.id)" class="text-white/20 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18"/></svg>
            </button>
        </div>
    </template>
</div>
