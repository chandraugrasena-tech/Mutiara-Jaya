
import { useState } from "react";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { useCart } from "../utils/CartContext";
import { useAuth } from "../utils/AuthContext";
import AddressModal from "./AddressModal";

export default function Navbar() {
  const {
    totalItems,
    selectedAddress,
    searchQuery,
    setSearchQuery,
  } = useCart();

  const { user, isAuthenticated, logout } = useAuth();

  const [showAddress, setShowAddress] = useState(false);
  const [loggingOut, setLoggingOut] = useState(false);

  const { pathname } = useLocation();
  const navigate = useNavigate();

  const handleSearch = (e) => {
    setSearchQuery(e.target.value);
    if (pathname !== "/") navigate("/");
  };

  const navClass = (path) =>
    `hover:text-green-200 transition-colors ${
      pathname === path
        ? "underline underline-offset-4 decoration-2"
        : ""
    }`;

  const badge =
    totalItems > 0 ? (
      <span className="bg-red-500 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-full">
        {totalItems}
      </span>
    ) : null;

  const handleLogout = async () => {
    if (!window.confirm("Apakah kamu yakin ingin keluar?")) return;

    setLoggingOut(true);

    try {
      await logout();
      navigate("/", { replace: true });
    } catch {
      navigate("/", { replace: true });
    } finally {
      setLoggingOut(false);
    }
  };

  return (
    <>
      <header className="sticky top-0 z-50 bg-[#165823] text-white shadow-lg">
        <div className="max-w-7xl mx-auto px-4 py-3 flex flex-wrap items-center gap-x-3 gap-y-2">

          {/* Logo */}
          <Link
            to="/"
            className="font-black text-lg sm:text-xl tracking-wide shrink-0"
          >
            Mutiara Jaya
          </Link>

          {/* Alamat pengiriman */}
          <button
            type="button"
            onClick={() => setShowAddress(true)}
            className="order-3 w-full md:order-none md:w-auto flex items-center gap-1.5 bg-[#1d6b2c] hover:bg-[#258338] px-3 py-1.5 rounded-full text-xs font-semibold transition-all border border-green-400/40 cursor-pointer"
          >
            <span className="text-green-200 shrink-0">
              Antar ke:
            </span>
            <span className="truncate md:max-w-[200px]">
              {selectedAddress
                ? `${selectedAddress.label} • ${selectedAddress.detail}`
                : "Pilih alamat"}
            </span>
          </button>

          {/* Pencarian */}
          <div className="order-4 w-full md:order-none md:w-auto md:flex-1 md:max-w-md md:mx-2">
            <input
              type="text"
              value={searchQuery}
              onChange={handleSearch}
              placeholder="Mau makan apa hari ini?"
              className="w-full bg-white text-gray-800 text-xs sm:text-sm px-4 py-2 rounded-full focus:outline-none shadow-inner"
            />
          </div>

          {/* Navigasi desktop */}
          <nav className="hidden md:flex items-center gap-5 ml-auto text-sm font-bold">

            <Link to="/" className={navClass("/")}>
              Jelajah
            </Link>

            <Link to="/promo" className={navClass("/promo")}>
              Promo
            </Link>

            <Link
              to="/cart"
              className={`${navClass("/cart")} flex items-center gap-1.5`}
            >
              Keranjang
              {badge}
            </Link>

            <Link to="/about" className={navClass("/about")}>
              Tentang
            </Link>

            {!isAuthenticated ? (
              <>
                <Link to="/login" className={navClass("/login")}>
                  Masuk
                </Link>

                <Link
                  to="/register"
                  className="bg-white text-[#165823] px-4 py-2 rounded-full hover:bg-green-100 transition-colors"
                >
                  Daftar
                </Link>
              </>
            ) : (
              <>
                <Link to="/orders" className={navClass("/orders")}>
                  Pesanan
                </Link>

                <button
                  type="button"
                  onClick={handleLogout}
                  disabled={loggingOut}
                  className="hover:text-green-200 disabled:opacity-50"
                >
                  {loggingOut ? "Keluar..." : "Keluar"}
                </button>
              </>
            )}
          </nav>
        </div>
      </header>

      {showAddress && (
        <AddressModal onClose={() => setShowAddress(false)} />
      )}
    </>
  );
}
