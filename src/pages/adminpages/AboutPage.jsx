import { useState } from "react";
import { Link } from "react-router-dom";
import { products } from "../../utils/data";
import { useCart } from "../../utils/CartContext";

const INFO = {
  version: "v1.0.0",
  updated: "8 Oktober 2026",
  area: "Singaraja",
  admins: 1,
  phone: "0895-6227-69692",
  whatsapp: "0895622759692",
  email: "admin@mutiarajaya.id",
};

const TECH = [
  { name: "React", ver: "18.3", icon: "⚛️", color: "bg-sky-100 text-sky-700" },
  { name: "Vite", ver: "5.4", icon: "⚡", color: "bg-purple-100 text-purple-700" },
  { name: "Tailwind CSS", ver: "3.4", icon: "🎨", color: "bg-cyan-100 text-cyan-700" },
  { name: "React Router", ver: "6.22", icon: "🧭", color: "bg-rose-100 text-rose-700" },
];

const FEATURES = [
  {
    icon: "📦",
    title: "Manajemen Pesanan Masuk",
    desc: "Pesanan dari pelanggan langsung tampil di Dashboard admin beserta total dan metode bayarnya.",
    tag: "Simulasi",
  },
  {
    icon: "💳",
    title: "Status Pembayaran & Pengiriman",
    desc: "Status pesanan berubah bertahap (Diproses, lalu Selesai) berdasarkan waktu pesanan dibuat.",
    tag: "Simulasi",
  },
  {
    icon: "💾",
    title: "Sinkronisasi Penyimpanan Lokal",
    desc: "Data pesanan, keranjang, alamat, dan promo disimpan di localStorage sehingga tidak hilang saat refresh.",
    tag: "Aktif",
  },
];

const FAQ = [
  {
    q: "Pesanan baru tidak muncul di Dashboard admin?",
    a: "Pastikan pesanan dibuat di browser yang sama dengan halaman admin, karena data tersimpan di localStorage browser. Setelah itu muat ulang halaman Dashboard.",
  },
  {
    q: "Bagaimana cara memproses pesanan masuk?",
    a: "Buka Dashboard, cek daftar Pesanan Masuk, lalu perhatikan alamat, jumlah item, dan metode pembayaran. Pesanan QRIS dianggap sudah dibayar, sedangkan CASH dibayar di tempat.",
  },
  {
    q: "Data pesanan hilang atau ingin dikosongkan?",
    a: "Buka DevTools browser (F12), pilih Application, lalu Local Storage, dan hapus data berawalan mj_ (mj_orders untuk pesanan). Muat ulang halaman setelahnya.",
  },
];

export default function AboutPage() {
  const { orders } = useCart();
  const [open, setOpen] = useState(0);

  const stats = [
    { icon: "🍽️", label: "Total Menu", value: products.length },
    { icon: "🧾", label: "Pesanan Masuk", value: orders.length },
    { icon: "👤", label: "Admin Terdaftar", value: INFO.admins },
    { icon: "📍", label: "Wilayah Antar", value: INFO.area },
  ];

  return (
    <div className="max-w-5xl mx-auto space-y-6">
      <div className="bg-gradient-to-r from-[#165823] to-[#218335] text-white rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div className="flex items-center gap-4">
          <div className="w-14 h-14 rounded-2xl bg-white/15 flex items-center justify-center text-3xl">🍲</div>
          <div>
            <h1 className="text-2xl font-extrabold">Mutiara Jaya</h1>
            <p className="text-sm text-green-100">Panel Admin • Pesan Antar Cepat & Hangat</p>
          </div>
        </div>
        <div className="flex flex-col sm:items-end gap-1">
          <span className="inline-flex items-center gap-2 bg-white/15 px-3 py-1.5 rounded-full text-xs font-bold w-fit">
            <span className="relative flex h-2.5 w-2.5">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-300 opacity-75"></span>
              <span className="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-400"></span>
            </span>
            System Active
          </span>
          <p className="text-xs text-green-100">
            Versi {INFO.version} • Diperbarui {INFO.updated}
          </p>
        </div>
      </div>

      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {stats.map((s) => (
          <div key={s.label} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
            <div className="text-2xl">{s.icon}</div>
            <div className="min-w-0">
              <p className="text-[11px] text-gray-500">{s.label}</p>
              <p className="text-lg font-extrabold text-gray-800 truncate">{s.value}</p>
            </div>
          </div>
        ))}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
          <div>
            <h2 className="font-bold text-lg text-gray-800 mb-2">Profil Singkat</h2>
            <p className="text-sm text-gray-600 leading-relaxed">
              Mutiara Jaya adalah layanan pesan antar makanan yang menyajikan menu rumahan seperti ayam geprek,
              nasi campur, sate, aneka minuman, dan camilan. Pelanggan dapat memesan lewat website, memilih
              alamat pengantaran, memakai promo, lalu memantau pesanan sampai tiba.
            </p>
          </div>
        </div>

        <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3">
          <h2 className="font-bold text-lg text-gray-800">Kontak Operasional</h2>
          <p className="text-xs text-gray-500">Hubungi penanggung jawab jika ada kendala sistem.</p>
          <a
            href={`tel:${INFO.phone.replace(/-/g, "")}`}
            className="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 text-sm"
          >
            <span className="text-lg">📞</span>
            <div>
              <p className="text-[11px] text-gray-500">Nomor Admin</p>
              <p className="font-bold text-gray-800">{INFO.phone}</p>
            </div>
          </a>
          <a
            href={`https://wa.me/${INFO.whatsapp}`}
            target="_blank"
            rel="noreferrer"
            className="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 text-sm"
          >
            <span className="text-lg">💬</span>
            <div>
              <p className="text-[11px] text-gray-500">WhatsApp Support</p>
              <p className="font-bold text-gray-800">Chat sekarang</p>
            </div>
          </a>
          <a
            href={`mailto:${INFO.email}`}
            className="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 text-sm"
          >
            <span className="text-lg">✉️</span>
            <div className="min-w-0">
              <p className="text-[11px] text-gray-500">Email Penanggung Jawab</p>
              <p className="font-bold text-gray-800 truncate">{INFO.email}</p>
            </div>
          </a>
        </div>
      </div>

      <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        <div>
          <h2 className="font-bold text-lg text-gray-800 mb-3">Teknologi Utama</h2>
          <div className="flex flex-wrap gap-2">
            {TECH.map((t) => (
              <span
                key={t.name}
                className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold ${t.color}`}
              >
                <span>{t.icon}</span>
                {t.name}
                <span className="opacity-60 font-semibold">v{t.ver}</span>
              </span>
            ))}
          </div>
        </div>

        <div>
          <h2 className="font-bold text-lg text-gray-800 mb-3">Fitur Utama Admin</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            {FEATURES.map((f) => (
              <div key={f.title} className="border border-gray-200 rounded-xl p-4 space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-2xl">{f.icon}</span>
                  <span
                    className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${
                      f.tag === "Aktif" ? "bg-green-100 text-green-700" : "bg-yellow-100 text-yellow-700"
                    }`}
                  >
                    {f.tag}
                  </span>
                </div>
                <h3 className="font-bold text-sm text-gray-800">{f.title}</h3>
                <p className="text-xs text-gray-600 leading-relaxed">{f.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </div>

      <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
        <h2 className="font-bold text-lg text-gray-800">Panduan Ringkas Admin (FAQ)</h2>
        <div className="divide-y divide-gray-100 border border-gray-200 rounded-xl overflow-hidden">
          {FAQ.map((item, i) => (
            <div key={item.q}>
              <button
                type="button"
                onClick={() => setOpen(open === i ? -1 : i)}
                className="w-full flex items-center justify-between gap-3 text-left px-4 py-3 text-sm font-semibold text-gray-800 hover:bg-gray-50 cursor-pointer"
              >
                {item.q}
                <span className="text-gray-400 shrink-0">{open === i ? "▲" : "▼"}</span>
              </button>
              {open === i && <p className="px-4 pb-4 text-sm text-gray-600 leading-relaxed">{item.a}</p>}
            </div>
          ))}
        </div>

        <div className="pt-2">
          <h3 className="font-bold text-sm text-gray-800 mb-2">Tautan Penting</h3>
          <div className="flex flex-wrap gap-3">
            <Link
              to="/"
              className="bg-[#165823] hover:bg-[#12481c] text-white text-xs font-bold px-4 py-2.5 rounded-xl"
            >
              ← Return to Storefront
            </Link>
            <Link
              to="/admin/dashboard"
              className="border border-[#165823] text-[#165823] hover:bg-green-50 text-xs font-bold px-4 py-2.5 rounded-xl"
            >
              📊 Dashboard Admin
            </Link>
            <Link
              to="/promo"
              className="border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-bold px-4 py-2.5 rounded-xl"
            >
              🏷️ Lihat Halaman Promo
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
