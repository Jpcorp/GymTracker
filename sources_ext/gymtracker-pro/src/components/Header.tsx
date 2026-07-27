import React, { useState } from 'react';
import {
  Dumbbell,
  Users,
  Search,
  Plus,
  CalendarCheck,
  Bell,
  RefreshCw,
  Sparkles,
  Zap,
  Award
} from 'lucide-react';
import { useGym } from '../context/GymContext';

interface HeaderProps {
  activeView: 'dashboard' | 'clients' | 'profile' | 'reports';
  setActiveView: (view: 'dashboard' | 'clients' | 'profile' | 'reports') => void;
  onOpenNewClient: () => void;
  onOpenCheckIn: () => void;
  onOpenNewMetric: () => void;
}

export const Header: React.FC<HeaderProps> = ({
  activeView,
  setActiveView,
  onOpenNewClient,
  onOpenCheckIn,
  onOpenNewMetric
}) => {
  const { clients, selectedClientId, setSelectedClientId, resetToDefaultData, evaluations } = useGym();
  const [searchQuery, setSearchQuery] = useState('');
  const [showSearchResults, setShowSearchResults] = useState(false);
  const [showNotifications, setShowNotifications] = useState(false);

  const activeClientsCount = clients.filter((c) => c.status === 'active').length;
  
  // Pending 21-day evaluation alerts
  const pendingEvalsCount = clients.filter(c => {
    const start = new Date(c.startDate);
    const today = new Date();
    const days = Math.floor((today.getTime() - start.getTime()) / (1000 * 60 * 60 * 24));
    return days > 0 && days % 21 <= 3; // due in 3 days or less
  }).length;

  const searchFilteredClients = searchQuery.trim()
    ? clients.filter(
        (c) =>
          c.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
          c.email.toLowerCase().includes(searchQuery.toLowerCase()) ||
          c.phone.includes(searchQuery)
      )
    : [];

  return (
    <header className="bg-slate-900 text-white border-b border-slate-800 sticky top-0 z-40 shadow-md">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16 gap-4">
          
          {/* Logo & Brand */}
          <div className="flex items-center gap-3">
            <button
              onClick={() => setActiveView('dashboard')}
              className="flex items-center gap-2 text-left group focus:outline-none"
            >
              <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-white font-bold shadow-lg shadow-cyan-500/20 group-hover:scale-105 transition-transform">
                <Dumbbell className="w-6 h-6" />
              </div>
              <div>
                <div className="flex items-center gap-1.5">
                  <span className="font-extrabold text-lg tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent">
                    GymTracker
                  </span>
                  <span className="text-xs font-semibold px-1.5 py-0.5 rounded bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">
                    PRO
                  </span>
                </div>
                <p className="text-[11px] text-slate-400 hidden sm:block">
                  Control de 21 Días & Sincronización
                </p>
              </div>
            </button>

            {/* Navigation tabs */}
            <nav className="hidden md:flex items-center gap-1 ml-6 bg-slate-800/80 p-1 rounded-lg border border-slate-700/60">
              <button
                onClick={() => setActiveView('dashboard')}
                className={`px-3 py-1.5 rounded-md text-xs font-medium transition-colors ${
                  activeView === 'dashboard'
                    ? 'bg-cyan-500 text-slate-950 font-semibold shadow'
                    : 'text-slate-300 hover:text-white hover:bg-slate-700/50'
                }`}
              >
                Dashboard
              </button>
              <button
                onClick={() => setActiveView('clients')}
                className={`px-3 py-1.5 rounded-md text-xs font-medium transition-colors flex items-center gap-1.5 ${
                  activeView === 'clients'
                    ? 'bg-cyan-500 text-slate-950 font-semibold shadow'
                    : 'text-slate-300 hover:text-white hover:bg-slate-700/50'
                }`}
              >
                <Users className="w-3.5 h-3.5" />
                Clientes ({activeClientsCount}/100)
              </button>
              <button
                onClick={() => setActiveView('profile')}
                className={`px-3 py-1.5 rounded-md text-xs font-medium transition-colors flex items-center gap-1.5 ${
                  activeView === 'profile'
                    ? 'bg-cyan-500 text-slate-950 font-semibold shadow'
                    : 'text-slate-300 hover:text-white hover:bg-slate-700/50'
                }`}
              >
                Perfil Alumno
              </button>
            </nav>
          </div>

          {/* Quick Search */}
          <div className="relative flex-1 max-w-xs hidden lg:block">
            <div className="relative">
              <Search className="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input
                type="text"
                placeholder="Buscar alumno por nombre..."
                value={searchQuery}
                onChange={(e) => {
                  setSearchQuery(e.target.value);
                  setShowSearchResults(true);
                }}
                onFocus={() => setShowSearchResults(true)}
                className="w-full bg-slate-800 text-sm text-slate-200 placeholder-slate-400 pl-9 pr-4 py-1.5 rounded-lg border border-slate-700 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition"
              />
            </div>

            {/* Live Search dropdown */}
            {showSearchResults && searchQuery.trim() && (
              <div className="absolute top-full left-0 right-0 mt-1 bg-slate-800 border border-slate-700 rounded-lg shadow-xl overflow-hidden z-50">
                {searchFilteredClients.length > 0 ? (
                  searchFilteredClients.map((client) => (
                    <button
                      key={client.id}
                      onClick={() => {
                        setSelectedClientId(client.id);
                        setActiveView('profile');
                        setShowSearchResults(false);
                        setSearchQuery('');
                      }}
                      className="w-full text-left px-3 py-2 text-xs flex items-center justify-between hover:bg-slate-700 border-b border-slate-700/50 last:border-0"
                    >
                      <div className="flex items-center gap-2">
                        <img
                          src={client.profilePhoto || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=100'}
                          alt={client.name}
                          className="w-6 h-6 rounded-full object-cover"
                        />
                        <div>
                          <p className="font-medium text-slate-200">{client.name}</p>
                          <p className="text-[10px] text-slate-400">{client.goal}</p>
                        </div>
                      </div>
                      <span className="text-[10px] px-1.5 py-0.5 rounded bg-slate-700 text-slate-300">
                        {client.status}
                      </span>
                    </button>
                  ))
                ) : (
                  <div className="p-3 text-xs text-slate-400 text-center">
                    No se encontraron alumnos
                  </div>
                )}
              </div>
            )}
          </div>

          {/* Action buttons & Realtime indicator */}
          <div className="flex items-center gap-2">
            
            {/* Realtime status pulse */}
            <div className="hidden xl:flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[11px] font-medium">
              <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
              Sincronizado
            </div>

            {/* Check-In button */}
            <button
              onClick={onOpenCheckIn}
              className="hidden sm:flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg border border-slate-700 transition"
              title="Registrar Asistencia del Alumno"
            >
              <CalendarCheck className="w-3.5 h-3.5 text-cyan-400" />
              <span>Check-in</span>
            </button>

            {/* New Client Button */}
            <button
              onClick={onOpenNewClient}
              className="flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs rounded-lg shadow-md transition"
            >
              <Plus className="w-4 h-4 stroke-[3]" />
              <span className="hidden sm:inline">Nuevo Cliente</span>
            </button>

            {/* Notifications Bell */}
            <div className="relative">
              <button
                onClick={() => setShowNotifications(!showNotifications)}
                className="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 relative"
                title="Notificaciones de Evaluaciones 21 Días"
              >
                <Bell className="w-4 h-4" />
                {pendingEvalsCount > 0 && (
                  <span className="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center animate-bounce">
                    {pendingEvalsCount}
                  </span>
                )}
              </button>

              {/* Notifications Dropdown */}
              {showNotifications && (
                <div className="absolute right-0 mt-2 w-80 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl p-3 z-50 text-xs">
                  <div className="flex items-center justify-between pb-2 border-b border-slate-700 mb-2">
                    <span className="font-semibold text-white flex items-center gap-1.5">
                      <Zap className="w-3.5 h-3.5 text-amber-400" />
                      Alertas de Control (21 Días)
                    </span>
                    <span className="text-[10px] text-slate-400">{pendingEvalsCount} pendientes</span>
                  </div>

                  <div className="space-y-2 max-h-60 overflow-y-auto">
                    {pendingEvalsCount > 0 ? (
                      clients
                        .filter(c => {
                          const start = new Date(c.startDate);
                          const today = new Date();
                          const days = Math.floor((today.getTime() - start.getTime()) / (1000 * 60 * 60 * 24));
                          return days > 0 && days % 21 <= 3;
                        })
                        .map((c) => (
                          <div
                            key={c.id}
                            onClick={() => {
                              setSelectedClientId(c.id);
                              setActiveView('profile');
                              setShowNotifications(false);
                            }}
                            className="p-2 rounded-lg bg-slate-900/80 hover:bg-slate-700 cursor-pointer border border-slate-700/50 flex items-center justify-between"
                          >
                            <div>
                              <p className="font-medium text-slate-200">{c.name}</p>
                              <p className="text-[10px] text-amber-400 font-medium">
                                Próxima Evaluación de 21 días pendiente
                              </p>
                            </div>
                            <button
                              onClick={(e) => {
                                e.stopPropagation();
                                setSelectedClientId(c.id);
                                onOpenNewMetric();
                                setShowNotifications(false);
                              }}
                              className="px-2 py-1 bg-cyan-500/20 text-cyan-300 hover:bg-cyan-500/30 rounded text-[10px] font-semibold border border-cyan-500/30"
                            >
                              + InBody
                            </button>
                          </div>
                        ))
                    ) : (
                      <p className="text-slate-400 text-center py-3 text-[11px]">
                        No hay evaluaciones de 21 días vencidas hoy.
                      </p>
                    )}
                  </div>

                  <div className="mt-2 pt-2 border-t border-slate-700 flex justify-between items-center text-[10px] text-slate-400">
                    <button
                      onClick={resetToDefaultData}
                      className="text-slate-400 hover:text-cyan-400 flex items-center gap-1"
                    >
                      <RefreshCw className="w-3 h-3" /> Reiniciar Datos Demo
                    </button>
                  </div>
                </div>
              )}
            </div>

          </div>

        </div>
      </div>
    </header>
  );
};
