import React, { useState, useEffect } from 'react';
import { Client, Gender, ClientStatus } from '../types';
import { useGym } from '../context/GymContext';
import { User, X, Plus } from 'lucide-react';

interface NewClientModalProps {
  isOpen: boolean;
  onClose: () => void;
  clientToEdit?: Client | null;
}

export const NewClientModal: React.FC<NewClientModalProps> = ({
  isOpen,
  onClose,
  clientToEdit
}) => {
  const { addClient, updateClient } = useGym();

  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [gender, setGender] = useState<Gender>('male');
  const [birthDate, setBirthDate] = useState('1998-01-01');
  const [startDate, setStartDate] = useState(new Date().toISOString().split('T')[0]);
  const [heightCm, setHeightCm] = useState(175);
  const [goal, setGoal] = useState('Recomposición Corporal & Hipertrofia');
  const [profilePhoto, setProfilePhoto] = useState('');
  const [status, setStatus] = useState<ClientStatus>('active');
  const [trainerName, setTrainerName] = useState('Carlos Entrenador');
  const [notes, setNotes] = useState('');

  useEffect(() => {
    if (clientToEdit) {
      setName(clientToEdit.name);
      setEmail(clientToEdit.email);
      setPhone(clientToEdit.phone);
      setGender(clientToEdit.gender);
      setBirthDate(clientToEdit.birthDate);
      setStartDate(clientToEdit.startDate);
      setHeightCm(clientToEdit.heightCm || 175);
      setGoal(clientToEdit.goal);
      setProfilePhoto(clientToEdit.profilePhoto || '');
      setStatus(clientToEdit.status);
      setTrainerName(clientToEdit.trainerName);
      setNotes(clientToEdit.notes || '');
    } else {
      setName('');
      setEmail('');
      setPhone('+56 9 ');
      setGender('male');
      setBirthDate('1998-05-10');
      setStartDate(new Date().toISOString().split('T')[0]);
      setHeightCm(175);
      setGoal('Recomposición Corporal & Hipertrofia');
      setProfilePhoto('');
      setStatus('active');
      setTrainerName('Carlos Entrenador');
      setNotes('');
    }
  }, [clientToEdit, isOpen]);

  if (!isOpen) return null;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!name.trim()) return;

    if (clientToEdit) {
      updateClient(clientToEdit.id, {
        name,
        email,
        phone,
        gender,
        birthDate,
        startDate,
        heightCm,
        goal,
        profilePhoto: profilePhoto || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=300',
        status,
        trainerName,
        notes
      });
    } else {
      addClient({
        name,
        email,
        phone,
        gender,
        birthDate,
        startDate,
        heightCm,
        goal,
        profilePhoto: profilePhoto || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=300',
        status,
        trainerName,
        notes
      });
    }

    onClose();
  };

  return (
    <div className="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
      <div className="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-lg w-full space-y-5 shadow-2xl relative my-8">
        
        <div className="flex items-center justify-between pb-3 border-b border-slate-800">
          <div className="flex items-center gap-2">
            <User className="w-5 h-5 text-cyan-400" />
            <h3 className="font-bold text-base text-white">
              {clientToEdit ? 'Editar Perfil del Alumno' : 'Nuevo Cliente de Entrenamiento'}
            </h3>
          </div>
          <button
            onClick={onClose}
            className="p-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4 text-xs">
          
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label className="block text-slate-300 font-semibold mb-1">Nombre Completo *</label>
              <input
                type="text"
                required
                placeholder="Ej: Juan Pérez"
                value={name}
                onChange={(e) => setName(e.target.value)}
                className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-medium"
              />
            </div>

            <div>
              <label className="block text-slate-300 font-semibold mb-1">Correo Electrónico</label>
              <input
                type="email"
                placeholder="juan@email.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-medium"
              />
            </div>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label className="block text-slate-300 font-semibold mb-1">Teléfono</label>
              <input
                type="text"
                value={phone}
                onChange={(e) => setPhone(e.target.value)}
                className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-medium"
              />
            </div>

            <div>
              <label className="block text-slate-300 font-semibold mb-1">Género</label>
              <select
                value={gender}
                onChange={(e) => setGender(e.target.value as Gender)}
                className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-medium"
              >
                <option value="male">Masculino</option>
                <option value="female">Femenino</option>
                <option value="other">Otro</option>
              </select>
            </div>

            <div>
              <label className="block text-slate-300 font-semibold mb-1">Estatura (cm)</label>
              <input
                type="number"
                value={heightCm}
                onChange={(e) => setHeightCm(Number(e.target.value))}
                className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-medium"
              />
            </div>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label className="block text-slate-300 font-semibold mb-1">Fecha de Nacimiento</label>
              <input
                type="date"
                value={birthDate}
                onChange={(e) => setBirthDate(e.target.value)}
                className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-medium"
              />
            </div>

            <div>
              <label className="block text-slate-300 font-semibold mb-1">Fecha de Inicio en Gym *</label>
              <input
                type="date"
                required
                value={startDate}
                onChange={(e) => setStartDate(e.target.value)}
                className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-medium"
              />
            </div>
          </div>

          <div>
            <label className="block text-slate-300 font-semibold mb-1">Objetivo del Alumno</label>
            <input
              type="text"
              placeholder="Ej: Recomposición corporal & Aumento de fuerza"
              value={goal}
              onChange={(e) => setGoal(e.target.value)}
              className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-medium"
            />
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label className="block text-slate-300 font-semibold mb-1">Entrenador Asignado</label>
              <input
                type="text"
                value={trainerName}
                onChange={(e) => setTrainerName(e.target.value)}
                className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-medium"
              />
            </div>

            <div>
              <label className="block text-slate-300 font-semibold mb-1">Estado</label>
              <select
                value={status}
                onChange={(e) => setStatus(e.target.value as ClientStatus)}
                className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-medium"
              >
                <option value="active">Activo</option>
                <option value="paused">Pausado</option>
                <option value="inactive">Inactivo</option>
              </select>
            </div>
          </div>

          <div>
            <label className="block text-slate-300 font-semibold mb-1">Notas Clínicas / Lesiones</label>
            <textarea
              rows={2}
              placeholder="Escribir observaciones o antecedentes de salud..."
              value={notes}
              onChange={(e) => setNotes(e.target.value)}
              className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-medium resize-none"
            />
          </div>

          <div className="pt-3 flex justify-end gap-2">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl transition"
            >
              Cancelar
            </button>
            <button
              type="submit"
              className="px-5 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-extrabold rounded-xl shadow transition"
            >
              {clientToEdit ? 'Guardar Cambios' : 'Registrar Cliente'}
            </button>
          </div>

        </form>

      </div>
    </div>
  );
};
