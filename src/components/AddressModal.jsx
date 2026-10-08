import { useState } from "react";
import { useCart } from "../utils/CartContext";

const LABELS = ["Rumah", "Kantor", "Lainnya"];
const EMPTY = { id: null, label: "Rumah", detail: "" };

export default function AddressModal({ onClose }) {
  const { addresses, selectedAddress, selectAddress, saveAddress, deleteAddress } = useCart();
  const [mode, setMode] = useState("list");
  const [form, setForm] = useState(EMPTY);

  const openForm = (addr) => {
    setForm(addr ? { ...addr } : EMPTY);
    setMode("form");
  };

  const handlePick = (id) => {
    selectAddress(id);
    onClose();
  };

  const handleSave = () => {
    if (!form.detail.trim()) return;
    saveAddress({ ...form, detail: form.detail.trim() });
    onClose();
  };

  return (
    <div
      className="fixed inset-0 bg-black/60 z-[100] flex items-end sm:items-center justify-center sm:p-4"
      onClick={onClose}
    >
      <div
        className="bg-white text-gray-800 w-full max-w-md rounded-t-3xl sm:rounded-2xl p-5 shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto"
        onClick={(e) => e.stopPropagation()}
      >
        <div className="flex justify-between items-center border-b pb-3">
          <h3 className="font-extrabold text-base text-gray-900">
            {mode === "list" ? "📍 Anter ke mana?" : form.id ? "Ubah alamat" : "Tambah alamat baru"}
          </h3>
          <button
            type="button"
            onClick={onClose}
            className="text-gray-400 hover:text-gray-600 font-bold text-lg cursor-pointer"
            aria-label="Tutup"
          >
            ✕
          </button>
        </div>

        {mode === "list" ? (
          <>
            <div className="space-y-2">
              {addresses.map((a) => {
                const isSelected = selectedAddress?.id === a.id;
                return (
                  <div
                    key={a.id}
                    onClick={() => handlePick(a.id)}
                    className={`p-3 rounded-xl border flex items-start gap-3 cursor-pointer transition-all ${
                      isSelected ? "border-[#165823] bg-green-50/60" : "border-gray-200 hover:border-gray-300"
                    }`}
                  >
                    <div
                      className={`mt-0.5 w-4 h-4 shrink-0 rounded-full border-2 flex items-center justify-center ${
                        isSelected ? "border-[#165823]" : "border-gray-300"
                      }`}
                    >
                      {isSelected && <div className="w-2 h-2 rounded-full bg-[#165823]" />}
                    </div>
                    <div className="flex-1 min-w-0">
                      <p className="text-xs font-extrabold text-gray-800">{a.label}</p>
                      <p className="text-xs text-gray-600 break-words">{a.detail}</p>
                    </div>
                    <div className="flex flex-col items-end gap-1 shrink-0">
                      <button
                        type="button"
                        onClick={(e) => {
                          e.stopPropagation();
                          openForm(a);
                        }}
                        className="text-[11px] font-bold text-[#165823] hover:underline cursor-pointer"
                      >
                        Ubah
                      </button>
                      {addresses.length > 1 && (
                        <button
                          type="button"
                          onClick={(e) => {
                            e.stopPropagation();
                            deleteAddress(a.id);
                          }}
                          className="text-[11px] font-bold text-red-500 hover:underline cursor-pointer"
                        >
                          Hapus
                        </button>
                      )}
                    </div>
                  </div>
                );
              })}
            </div>

            <button
              type="button"
              onClick={() => openForm(null)}
              className="w-full py-3 rounded-xl border-2 border-dashed border-[#165823] text-[#165823] text-xs font-bold hover:bg-green-50 cursor-pointer"
            >
              + Tambah alamat baru
            </button>
          </>
        ) : (
          <>
            <div className="space-y-2">
              <label className="text-xs font-bold text-gray-600 block">Simpan sebagai</label>
              <div className="flex gap-2">
                {LABELS.map((l) => (
                  <button
                    key={l}
                    type="button"
                    onClick={() => setForm({ ...form, label: l })}
                    className={`px-3 py-1.5 rounded-full text-xs font-bold border cursor-pointer ${
                      form.label === l
                        ? "bg-[#165823] text-white border-[#165823]"
                        : "border-gray-300 text-gray-600 hover:bg-gray-50"
                    }`}
                  >
                    {l}
                  </button>
                ))}
              </div>
            </div>

            <div className="space-y-2">
              <label className="text-xs font-bold text-gray-600 block">Alamat lengkap</label>
              <textarea
                rows="3"
                value={form.detail}
                onChange={(e) => setForm({ ...form, detail: e.target.value })}
                placeholder="Nama jalan, nomor rumah, patokan..."
                className="w-full border border-gray-300 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#165823]"
                autoFocus
              />
            </div>

            <div className="flex justify-end gap-2 pt-2 border-t">
              <button
                type="button"
                onClick={() => setMode("list")}
                className="px-4 py-2 rounded-xl text-xs font-bold border border-gray-300 text-gray-600 hover:bg-gray-50 cursor-pointer"
              >
                Kembali
              </button>
              <button
                type="button"
                onClick={handleSave}
                disabled={!form.detail.trim()}
                className="bg-[#165823] text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-[#12481c] disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
              >
                Simpan alamat
              </button>
            </div>
          </>
        )}
      </div>
    </div>
  );
}
