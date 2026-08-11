<footer class="mt-14 border-t border-white/15 pt-8 text-sm text-slate-300">
    <div class="grid gap-8 pb-8 sm:grid-cols-2 lg:grid-cols-[1.2fr_.8fr_.9fr]">
        <div><img src="{{ asset('brand/perfil-log-blanco.png') }}" alt="Tienda Siete Market & Licorería" class="tienda-siete-wordmark h-10"><p class="mt-3 max-w-sm text-xs leading-relaxed text-slate-400">Sorteos organizados por TIENDAS SIETE S.A.C. Participa de forma informada y conserva tu comprobante de pago.</p></div>
        <div><p class="text-xs font-black uppercase tracking-[.16em] text-cyan-300">Legales</p><div class="mt-3 grid gap-2"><a href="{{ route('legal.terms') }}" class="hover:text-cyan-300">Términos y condiciones</a><a href="{{ route('legal.privacy') }}" class="hover:text-cyan-300">Política de privacidad</a><a href="{{ route('legal.cookies') }}" class="hover:text-cyan-300">Política de cookies</a></div></div>
        <div><p class="text-xs font-black uppercase tracking-[.16em] text-cyan-300">Contacto</p><div class="mt-3 grid gap-2 text-slate-300"><a href="mailto:sorteos@tiendasiete.com" class="hover:text-cyan-300">sorteos@tiendasiete.com</a><a href="tel:+51991077380" class="hover:text-cyan-300">991 077 380</a><p>RUC 20614107767</p></div></div>
    </div>
    <div class="flex flex-col gap-2 border-t border-white/10 py-5 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between"><p>© {{ now()->year }} TIENDAS SIETE S.A.C. Todos los derechos reservados.</p><p>RUC 20614107767</p></div>
</footer>
