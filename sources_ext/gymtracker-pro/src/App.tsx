import React, { useState } from 'react';
import { GymProvider, useGym } from './context/GymContext';
import { Header } from './components/Header';
import { DashboardOverview } from './components/DashboardOverview';
import { ClientHeader } from './components/ClientProfile/ClientHeader';
import { ClientMetricsTab } from './components/ClientProfile/ClientMetricsTab';
import { ClientPhotosTab } from './components/ClientProfile/ClientPhotosTab';
import { ClientRoutinesTab } from './components/ClientProfile/ClientRoutinesTab';
import { ClientEvaluationsTab } from './components/ClientProfile/ClientEvaluationsTab';
import { ClientChartsTab } from './components/ClientProfile/ClientChartsTab';
import { ClientAttendanceTab } from './components/ClientProfile/ClientAttendanceTab';
import { ClientWellnessTab } from './components/ClientProfile/ClientWellnessTab';

import { NewClientModal } from './components/NewClientModal';
import { NewMetricModal } from './components/NewMetricModal';
import { NewWorkoutLogModal } from './components/NewWorkoutLogModal';
import { CheckInModal } from './components/CheckInModal';
import { PdfExportModal } from './components/PdfExportModal';
import { RealtimeNotificationToast } from './components/RealtimeNotificationToast';

import { Client, Exercise } from './types';
import { Users, Plus, ChevronRight, Search } from 'lucide-react';

function GymAppContent() {
  const { clients, selectedClientId, setSelectedClientId, getClientById } = useGym();

  const [activeView, setActiveView] = useState<'dashboard' | 'clients' | 'profile' | 'reports'>('dashboard');
  const [profileTab, setProfileTab] = useState<string>('evaluations');

  // Modals state
  const [isNewClientOpen, setIsNewClientOpen] = useState(false);
  const [clientToEdit, setClientToEdit] = useState<Client | null>(null);

  const [isNewMetricOpen, setIsNewMetricOpen] = useState(false);
  const [isNewWorkoutLogOpen, setIsNewWorkoutLogOpen] = useState(false);
  const [selectedExerciseForLog, setSelectedExerciseForLog] = useState<Exercise | null>(null);

  const [isCheckInOpen, setIsCheckInOpen] = useState(false);
  const [isPdfReportOpen, setIsPdfReportOpen] = useState(false);

  const [clientsSearchTerm, setClientsSearchTerm] = useState('');

  const activeClient = getClientById(selectedClientId || '') || clients[0];

  const handleSelectClient = (clientId: string) => {
    setSelectedClientId(clientId);
    setActiveView('profile');
  };

  const handleOpenEditClient = () => {
    setClientToEdit(activeClient || null);
    setIsNewClientOpen(true);
  };

  const handleOpenNewClientModal = () => {
    setClientToEdit(null);
    setIsNewClientOpen(true);
  };

  const handleOpenNewWorkoutLog = (exercise?: Exercise) => {
    setSelectedExerciseForLog(exercise || null);
    setIsNewWorkoutLogOpen(true);
  };

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-cyan-500 selection:text-slate-950">
      
      {/* Top Bar Header */}
      <Header
        activeView={activeView}
        setActiveView={setActiveView}
        onOpenNewClient={handleOpenNewClientModal}
        onOpenCheckIn={() => setIsCheckInOpen(true)}
        onOpenNewMetric={() => setIsNewMetricOpen(true)}
      />

      {/* Main Content Body */}
      <main className="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        {/* VIEW 1: DASHBOARD */}
        {activeView === 'dashboard' && (
          <DashboardOverview
            onSelectClient={handleSelectClient}
            onOpenNewClient={handleOpenNewClientModal}
            onOpenCheckIn={() => setIsCheckInOpen(true)}
            onOpenNewMetric={() => setIsNewMetricOpen(true)}
          />
        )}

        {/* VIEW 2: CLIENTS LIST */}
        {activeView === 'clients' && (
          <div className="space-y-6">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-xl">
              <div>
                <h1 className="text-xl font-extrabold text-white flex items-center gap-2">
                  <Users className="w-5 h-5 text-cyan-400" />
                  Listado Completo de Alumnos ({clients.length}/100)
                </h1>
                <p className="text-xs text-slate-400 mt-1">
                  Gestiona el perfil individual, historial InBody y estado de cada cliente.
                </p>
              </div>

              <button
                onClick={handleOpenNewClientModal}
                className="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 text-slate-950 font-extrabold text-xs rounded-xl shadow transition flex items-center gap-1.5 self-start sm:self-auto"
              >
                <Plus className="w-4 h-4 stroke-[3]" />
                Nuevo Alumno
              </button>
            </div>

            {/* Search Input */}
            <div className="relative max-w-md">
              <Search className="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input
                type="text"
                placeholder="Filtrar por nombre, teléfono o correo..."
                value={clientsSearchTerm}
                onChange={(e) => setClientsSearchTerm(e.target.value)}
                className="w-full bg-slate-900 text-xs text-slate-200 placeholder-slate-400 pl-9 pr-4 py-2 rounded-xl border border-slate-800 focus:outline-none focus:border-cyan-500"
              />
            </div>

            {/* Clients Grid */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              {clients
                .filter(
                  (c) =>
                    c.name.toLowerCase().includes(clientsSearchTerm.toLowerCase()) ||
                    c.email.toLowerCase().includes(clientsSearchTerm.toLowerCase()) ||
                    c.phone.includes(clientsSearchTerm)
                )
                .map((client) => (
                  <div
                    key={client.id}
                    onClick={() => handleSelectClient(client.id)}
                    className="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg hover:border-cyan-500/40 cursor-pointer transition group flex flex-col justify-between"
                  >
                    <div className="space-y-3">
                      <div className="flex items-center gap-3">
                        <img
                          src={
                            client.profilePhoto ||
                            'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200'
                          }
                          alt={client.name}
                          className="w-12 h-12 rounded-full object-cover border border-slate-700 group-hover:border-cyan-400 transition"
                        />
                        <div>
                          <p className="font-bold text-sm text-white group-hover:text-cyan-400 transition">
                            {client.name}
                          </p>
                          <span
                            className={`text-[9px] font-bold px-2 py-0.5 rounded-full border ${
                              client.status === 'active'
                                ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30'
                                : 'bg-amber-500/20 text-amber-400 border-amber-500/30'
                            }`}
                          >
                            {client.status.toUpperCase()}
                          </span>
                        </div>
                      </div>

                      <p className="text-xs text-slate-300 font-medium line-clamp-1">
                        🎯 {client.goal}
                      </p>

                      <div className="text-[11px] text-slate-400 space-y-0.5">
                        <p>Entrenador: <strong className="text-slate-200">{client.trainerName}</strong></p>
                        <p>Inicio: {client.startDate}</p>
                      </div>
                    </div>

                    <div className="pt-4 mt-4 border-t border-slate-800/80 flex items-center justify-between text-xs font-semibold text-cyan-400">
                      <span>Ver Ficha Completa</span>
                      <ChevronRight className="w-4 h-4 group-hover:translate-x-1 transition" />
                    </div>
                  </div>
                ))}
            </div>
          </div>
        )}

        {/* VIEW 3: CLIENT PROFILE */}
        {activeView === 'profile' && activeClient && (
          <div className="space-y-6">
            
            {/* Profile Header */}
            <ClientHeader
              client={activeClient}
              activeTab={profileTab}
              setActiveTab={setProfileTab}
              onOpenNewMetric={() => setIsNewMetricOpen(true)}
              onOpenNewWorkoutLog={() => handleOpenNewWorkoutLog()}
              onOpenPdfReport={() => setIsPdfReportOpen(true)}
              onOpenEditClient={handleOpenEditClient}
            />

            {/* Profile Active Tab Body */}
            {profileTab === 'evaluations' && (
              <ClientEvaluationsTab
                client={activeClient}
                onOpenPdfReport={() => setIsPdfReportOpen(true)}
                onOpenNewMetric={() => setIsNewMetricOpen(true)}
              />
            )}

            {profileTab === 'metrics' && (
              <ClientMetricsTab
                client={activeClient}
                onOpenNewMetric={() => setIsNewMetricOpen(true)}
              />
            )}

            {profileTab === 'photos' && <ClientPhotosTab client={activeClient} />}

            {profileTab === 'routines' && (
              <ClientRoutinesTab
                client={activeClient}
                onOpenNewWorkoutLog={handleOpenNewWorkoutLog}
              />
            )}

            {profileTab === 'charts' && <ClientChartsTab client={activeClient} />}

            {profileTab === 'attendance' && (
              <ClientAttendanceTab
                client={activeClient}
                onOpenCheckIn={() => setIsCheckInOpen(true)}
              />
            )}

            {profileTab === 'wellness' && <ClientWellnessTab client={activeClient} />}

          </div>
        )}

      </main>

      {/* Modals */}
      <NewClientModal
        isOpen={isNewClientOpen}
        onClose={() => setIsNewClientOpen(false)}
        clientToEdit={clientToEdit}
      />

      <NewMetricModal
        isOpen={isNewMetricOpen}
        onClose={() => setIsNewMetricOpen(false)}
        clientId={activeClient?.id || null}
      />

      <NewWorkoutLogModal
        isOpen={isNewWorkoutLogOpen}
        onClose={() => setIsNewWorkoutLogOpen(false)}
        clientId={activeClient?.id || null}
        exercise={selectedExerciseForLog}
      />

      <CheckInModal
        isOpen={isCheckInOpen}
        onClose={() => setIsCheckInOpen(false)}
      />

      <PdfExportModal
        isOpen={isPdfReportOpen}
        onClose={() => setIsPdfReportOpen(false)}
        clientId={activeClient?.id || null}
      />

      {/* Realtime Toast Notifications */}
      <RealtimeNotificationToast />

      {/* Footer */}
      <footer className="border-t border-slate-900 bg-slate-950 py-4 text-center text-[11px] text-slate-500">
        GymTracker PRO © 2026 — Sistema de Gestión de Entrenamiento Personalizado & Control InBody (21 Días)
      </footer>

    </div>
  );
}

export default function App() {
  return (
    <GymProvider>
      <GymAppContent />
    </GymProvider>
  );
}
