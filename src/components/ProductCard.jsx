import React, { useState } from "react";
import { Link } from "react-router-dom";
import { useCart } from "../utils/CartContext";

export default function ProductCard({ p }) {
  const { addToCart } = useCart();
  const [added, setAdded] = useState(false);

  const handleAdd = () => {
    addToCart(p);
    setAdded(true);
    setTimeout(() => setAdded(false), 1200);
  };

  return (
    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col justify-between">
      <div>
        <div className="relative h-36 sm:h-44 w-full bg-gray-100 overflow-hidden">
          <img
            src={p.img}
            alt={p.name}
            className="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
          />
        </div>

        <div className="p-3">
          <div className="flex items-center gap-1 text-[11px] font-bold text-gray-500 mb-1">
            <span className="text-yellow-500">⭐ 4.8</span>
            <span>•</span>
            <span>{p.time || "15-25 min"}</span>
          </div>

          <h4 className="font-bold text-gray-800 text-sm sm:text-base line-clamp-1">
            {p.name}
          </h4>

          <p className="text-gray-500 text-xs line-clamp-2 mt-1">
            {p.description}
          </p>

          <div className="mt-3 font-extrabold text-gray-900 text-sm sm:text-base">
            Rp {p.price ? p.price.toLocaleString("id-ID") : "0"}
          </div>
        </div>
      </div>

      <div className="p-3 pt-0 flex gap-2 mt-2">
        <Link
          to={`/product/${p.id}`}
          className="flex-1 text-center border border-[#165823] text-[#165823] text-xs font-bold py-2 rounded-xl hover:bg-green-50 transition-colors flex items-center justify-center"
        >
          Detail
        </Link>
        <button
          onClick={handleAdd}
          className={`flex-1 text-white text-xs font-bold py-2 rounded-xl transition-all ${
            added ? "bg-emerald-700 scale-95" : "bg-[#165823] hover:bg-[#12481c]"
          }`}
        >
          {added ? "✓ Ditambah" : "+ Tambah"}
        </button>
      </div>
    </div>
  );
}