import React, { createContext, useContext, useEffect, useState } from "react";
import { PROMOS, getDiscount } from "./promos";

const CartContext = createContext(null);

const ONGKIR = 10000;

const DEFAULT_ADDRESSES = [
  { id: 1, label: "Rumah", detail: "Jl. Udayana No. 18, Singaraja" },
  { id: 2, label: "Kantor", detail: "Jl. Ahmad Yani No. 5, Singaraja" },
];

function usePersistentState(key, initial) {
  const [value, setValue] = useState(() => {
    try {
      const raw = localStorage.getItem(key);
      return raw ? JSON.parse(raw) : initial;
    } catch {
      return initial;
    }
  });
  useEffect(() => {
    try {
      localStorage.setItem(key, JSON.stringify(value));
    } catch {
    }
  }, [key, value]);
  return [value, setValue];
}

export function CartProvider({ children }) {
  const [cartItems, setCartItems] = usePersistentState("mj_cart", []);
  const [addresses, setAddresses] = usePersistentState("mj_addresses", DEFAULT_ADDRESSES);
  const [selectedAddressId, setSelectedAddressId] = usePersistentState("mj_address_id", 1);
  const [promoId, setPromoId] = usePersistentState("mj_promo", null);
  const [paymentMethod, setPaymentMethod] = usePersistentState("mj_payment", "CASH"); 
  const [orders, setOrders] = usePersistentState("mj_orders", []);
  const [searchQuery, setSearchQuery] = useState("");

  const addToCart = (product, qty = 1) => {
    setCartItems((prev) => {
      const exist = prev.find((i) => i.id === product.id);
      if (exist) {
        return prev.map((i) => (i.id === product.id ? { ...i, quantity: i.quantity + qty } : i));
      }
      return [...prev, { ...product, quantity: qty }];
    });
  };

  const updateQuantity = (id, delta) => {
    setCartItems((prev) =>
      prev
        .map((i) => {
          if (i.id !== id) return i;
          const q = i.quantity + delta;
          return q > 0 ? { ...i, quantity: q } : null;
        })
        .filter(Boolean)
    );
  };

  const removeFromCart = (id) => setCartItems((prev) => prev.filter((i) => i.id !== id));

  const selectedAddress =
    addresses.find((a) => a.id === selectedAddressId) || addresses[0] || null;

  const selectAddress = (id) => setSelectedAddressId(id);

  const saveAddress = ({ id, label, detail }) => {
    if (id) {
      setAddresses((prev) => prev.map((a) => (a.id === id ? { ...a, label, detail } : a)));
      setSelectedAddressId(id);
    } else {
      const newId = Date.now();
      setAddresses((prev) => [...prev, { id: newId, label, detail }]);
      setSelectedAddressId(newId);
    }
  };

  const deleteAddress = (id) => {
    if (addresses.length <= 1) return; // minimal 1 alamat
    const next = addresses.filter((a) => a.id !== id);
    setAddresses(next);
    if (id === selectedAddress?.id) setSelectedAddressId(next[0].id);
  };

  const selectPromo = (id) => setPromoId(id);

  const totalItems = cartItems.reduce((s, i) => s + i.quantity, 0);
  const subtotal = cartItems.reduce((s, i) => s + i.price * i.quantity, 0);
  const ongkir = cartItems.length > 0 ? ONGKIR : 0;

  const selectedPromo = PROMOS.find((p) => p.id === promoId) || null;
  const promoEligible = !!selectedPromo && subtotal >= selectedPromo.minOrder;
  const activePromo = promoEligible ? selectedPromo : null;
  const discount = getDiscount(activePromo, subtotal, ongkir);
  const total = subtotal > 0 ? subtotal + ongkir - discount : 0;

  const placeOrder = (notes = "") => {
    const id = "MJ" + Date.now().toString().slice(-7);
    const order = {
      id,
      items: cartItems,
      address: selectedAddress,
      paymentMethod,
      subtotal,
      ongkir,
      discount,
      total,
      notes,
      promoTitle: activePromo ? activePromo.title : null,
      createdAt: Date.now(),
    };
    setOrders((prev) => [order, ...prev]);
    setCartItems([]);
    setPromoId(null);
    return id;
  };

  return (
    <CartContext.Provider
      value={{
        cartItems,
        addToCart,
        updateQuantity,
        removeFromCart,
        totalItems,
        subtotal,
        ongkir,
        discount,
        total,
        addresses,
        selectedAddress,
        location: selectedAddress ? selectedAddress.detail : "",
        selectAddress,
        saveAddress,
        deleteAddress,
        promos: PROMOS,
        promoId,
        selectedPromo,
        activePromo,
        promoEligible,
        selectPromo,
        paymentMethod,
        setPaymentMethod,
        orders,
        placeOrder,
        searchQuery,
        setSearchQuery,
      }}
    >
      {children}
    </CartContext.Provider>
  );
}

export const useCart = () => useContext(CartContext);


