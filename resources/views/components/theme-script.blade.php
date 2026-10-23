{{--
    Shared by the panel and the blog so both use the same saved theme. Runs in <head>, before the
    first paint, so pages never flash in the wrong theme. window.irsoSetTheme() is what the switches call.
--}}
<script>
    (() => {
        const root = document.documentElement;
        const read = (key) => { try { return localStorage.getItem(key); } catch (error) { return null; } };

        root.classList.toggle('dark', read('irso-theme') === 'dark');
        root.classList.toggle('sidebar-hidden', read('irso-sidebar') === 'hidden');

        window.irsoSetTheme = (dark) => {
            root.classList.toggle('dark', dark);
            try { localStorage.setItem('irso-theme', dark ? 'dark' : 'light'); } catch (error) {}
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark } }));
        };

        // Keeps other open tabs in sync when the theme changes in one of them.
        window.addEventListener('storage', (event) => {
            if (event.key !== 'irso-theme') return;
            root.classList.toggle('dark', event.newValue === 'dark');
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: event.newValue === 'dark' } }));
        });
    })();
</script>
