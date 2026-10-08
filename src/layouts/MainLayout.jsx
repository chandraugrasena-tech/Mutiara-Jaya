import { Outlet, Link, useLocation } from "react-router-dom";
import Navbar from "../components/Navbar";
import { useCart } from "../utils/CartContext";

export default function MainLayout() {
  const { totalItems } = useCart();
  const { pathname } = useLocation();
  const isActive = (path) => pathname === path;

  const tab = (path, icon, label, badge) => (
    <Link
      to={path}
      className={`flex flex-col items-center text-xs relative ${
        isActive(path) ? "text-[#165823] font-bold" : "text-gray-400 font-medium"
      }`}
    >
      <span className="text-lg">{icon}</span>
      {label}
      {badge > 0 && (
        <span className="absolute -top-1 right-0 bg-[#ff788d] text-white text-[10px] font-extrabold px-1.5 rounded-full">
          {badge}
        </span>
      )}
    </Link>
  );

  return (
    <div className="flex flex-col min-h-screen bg-[#fff2f2] font-sans pb-20 md:pb-0">
      <Navbar />

      <main className="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
        <Outlet />
      </main>

      {/* Bottom navigation (HP) */}
      <nav className="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-6 py-2 flex justify-around items-center z-40 shadow-lg">
        {tab("/", "🍴", "Jelajahi")}
        {tab("/promo", "🏷️", "Promo")}
        {tab("/cart", "🛒", "Keranjang", totalItems)}
      </nav>

      {/* Footer (laptop) */}
      <footer className="hidden md:block bg-[#165823] text-[#fff2f2] py-6 mt-12 border-t border-green-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center gap-4 text-sm">
          <p className="font-bold text-base">🍲 Mutiara Jaya</p>
          <p>© 2026 Mutiara Jaya - Pesan Antar Cepat & Hangat</p>
        </div>
      </footer>
    </div>
  );
}
