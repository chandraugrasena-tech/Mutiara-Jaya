import { useState } from "react";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { useCart } from "../utils/CartContext";
import AddressModal from "./AddressModal";

export default function Navbar() {
  const { totalItems, selectedAddress, searchQuery, setSearchQuery } = useCart();
  const [showAddress, setShowAddress] = useState(false);
  const { pathname } = useLocation();
  const navigate = useNavigate();

  const handleSearch = (e) => {
    setSearchQuery(e.target.value);
    if (pathname !== "/") navigate("/");
  };

  const navClass = (path) =>
    `hover:text-green-200 transition-colors ${
      pathname === path ? "underline underline-offset-4 decoration-2" : ""
    }`;

  const badge =
    totalItems > 0 ? (
      <span className="bg-red-500 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-full">
        {totalItems}
      </span>
    ) : null;

  return (
    <>
      <header className="sticky top-0 z-50 bg-[#165823] text-white shadow-lg">
        <div className="max-w-7xl mx-auto px-4 py-3 flex flex-wrap items-center gap-x-3 gap-y-2">
          {/* Logo */}
          <Link to="/" className="font-black text-lg sm:text-xl tracking-wide flex items-center gap-2 shrink-0">
            <span>🍲</span>
            <span>Mutiara Jaya</span>
          </Link>

          <button
            type="button"
            onClick={() => setShowAddress(true)}
            className="order-3 w-full md:order-none md:w-auto flex items-center gap-1.5 bg-[#1d6b2c] hover:bg-[#258338] active:scale-95 px-3 py-1.5 rounded-full text-xs font-semibold transition-all border border-green-400/40 cursor-pointer shadow-sm"
          >
            <span className="text-green-200 shrink-0">Anter ke:</span>
            <span className="truncate md:max-w-[200px]">
              📍 {selectedAddress ? `${selectedAddress.label} • ${selectedAddress.detail}` : "Pilih alamat"}
            </span>
            <span className="text-[10px] ml-auto md:ml-0">▼</span>
          </button>

          {/* Pencarian */}
          <div className="order-4 w-full md:order-none md:w-auto md:flex-1 md:max-w-md md:mx-2">
            <div className="relative">
              <input
                type="text"
                value={searchQuery}
                onChange={handleSearch}
                placeholder="Mau makan apa hari ini?"
                className="w-full bg-white text-gray-800 text-xs sm:text-sm pl-9 pr-8 py-2 rounded-full focus:outline-none shadow-inner"
              />
              <span className="absolute left-3 top-2.5 text-gray-400 text-xs">🔍</span>
              {searchQuery && (
                <button
                  type="button"
                  onClick={() => setSearchQuery("")}
                  className="absolute right-3 top-2 text-gray-400 hover:text-gray-600 text-xs cursor-pointer"
                  aria-label="Hapus pencarian"
                >
                  ✕
                </button>
              )}
            </div>
          </div>

          <Link to="/cart" className="md:hidden ml-auto flex items-center gap-1.5 text-sm font-bold">
            <span>🛒</span>
            {badge}
          </Link>

          <nav className="hidden md:flex items-center gap-6 ml-auto text-sm font-bold">
            <Link to="/" className={navClass("/")}>Jelajahi</Link>
            <Link
              to="/promo"
              className={`hover:text-green-200 transition-colors ${
                pathname === "/promo" ? "underline underline-offset-4 decoration-2 text-yellow-300" : ""
              }`}
            >
              Promo
            </Link>
            <Link to="/cart" className={`${navClass("/cart")} flex items-center gap-1.5`}>
              <span>🛒 Keranjang</span>
              {badge}
            </Link>
          </nav>
        </div>
      </header>

      {showAddress && <AddressModal onClose={() => setShowAddress(false)} />}
    </>
  );
}
