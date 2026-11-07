
import { Outlet, Link, useLocation, useNavigate } from "react-router-dom";
import Navbar from "../components/Navbar";
import { useCart } from "../utils/CartContext";
import { useAuth } from "../utils/AuthContext";

export default function MainLayout() {
  const { totalItems } = useCart();
  const { isAuthenticated, logout } = useAuth();

  const { pathname } = useLocation();
  const navigate = useNavigate();

  const isActive = (path) => pathname === path;

  const tab = (path, label, badge) => (
    <Link
      to={path}
      className={`flex flex-col items-center justify-center text-[11px] relative ${
        isActive(path)
          ? "text-[#165823] font-bold"
          : "text-gray-500 font-medium"
      }`}
    >
      <span>{label}</span>

      {badge > 0 && (
        <span className="absolute -top-2 -right-3 bg-red-500 text-white text-[9px] font-extrabold px-1.5 rounded-full">
          {badge}
        </span>
      )}
    </Link>
  );

  const handleLogout = async () => {
    if (!window.confirm("Apakah kamu yakin ingin keluar?")) return;

    try {
      await logout();
    } finally {
      navigate("/", { replace: true });
    }
  };

  return (
    <div className="flex flex-col min-h-screen bg-[#fff2f2] font-sans pb-16 md:pb-0">
      <Navbar />

      <main className="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
        <Outlet />
      </main>

      {/* Navigasi HP */}
      <nav className="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-2 py-3 flex justify-around items-center z-40 shadow-lg">

        {tab("/", "Jelajah")}

        {tab("/promo", "Promo")}

        {tab("/cart", "Keranjang", totalItems)}

        {tab("/about", "Tentang")}

        {!isAuthenticated ? (
          <>
            {tab("/login", "Masuk")}
            {tab("/register", "Daftar")}
          </>
        ) : (
          <>
            {tab("/orders", "Pesanan")}

            <button
              type="button"
              onClick={handleLogout}
              className="text-[11px] text-gray-500 font-medium"
            >
              Keluar
            </button>
          </>
        )}
      </nav>

      {/* Footer laptop */}
      <footer className="hidden md:block bg-[#165823] text-[#fff2f2] py-6 mt-12 border-t border-green-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center gap-4 text-sm">
          <p className="font-bold text-base">Mutiara Jaya</p>
          <p>© 2026 Mutiara Jaya - Pesan Antar Cepat & Hangat</p>
        </div>
      </footer>
    </div>
  );
}
