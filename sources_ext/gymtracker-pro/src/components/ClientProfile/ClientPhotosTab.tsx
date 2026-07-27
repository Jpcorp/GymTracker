import React, { useState } from 'react';
import { Client, PoseType, BodyPhoto } from '../../types';
import { useGym } from '../../context/GymContext';
import {
  Camera,
  Plus,
  ArrowRight,
  Maximize2,
  Calendar,
  Sparkles,
  Layers,
  X,
  Upload
} from 'lucide-react';

interface ClientPhotosTabProps {
  client: Client;
}

export const ClientPhotosTab: React.FC<ClientPhotosTabProps> = ({ client }) => {
  const { getClientPhotos, addBodyPhoto } = useGym();
  const photos = getClientPhotos(client.id);

  // Group photos by photoDate
  const photoDates: string[] = Array.from(new Set<string>(photos.map((p) => p.photoDate))).sort(
    (a: string, b: string) => new Date(b).getTime() - new Date(a).getTime()
  );

  const [selectedDate1, setSelectedDate1] = useState<string>(photoDates[0] || client.startDate);
  const [selectedDate2, setSelectedDate2] = useState<string>(
    photoDates.length > 1 ? photoDates[photoDates.length - 1] : photoDates[0] || client.startDate
  );

  const [selectedPhotoModal, setSelectedPhotoModal] = useState<BodyPhoto | null>(null);
  const [showUploadModal, setShowUploadModal] = useState(false);

  // Upload Form State
  const [uploadPose, setUploadPose] = useState<PoseType>('front');
  const [uploadDate, setUploadDate] = useState<string>(new Date().toISOString().split('T')[0]);
  const [uploadPhotoUrl, setUploadPhotoUrl] = useState<string>('');
  const [uploadNotes, setUploadNotes] = useState<string>('');

  const poseLabels: Record<PoseType, string> = {
    front: 'Pose Frente',
    back: 'Pose Espalda',
    left_side: 'Pose Lado Izquierdo',
    right_side: 'Pose Lado Derecho'
  };

  const posePresets: Record<PoseType, string> = {
    front: 'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?auto=format&fit=crop&q=80&w=500',
    back: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&q=80&w=500',
    left_side: 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&q=80&w=500',
    right_side: 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&q=80&w=500'
  };

  const getPhotoForDateAndPose = (dateStr: string, pose: PoseType): BodyPhoto | undefined => {
    return photos.find((p) => p.photoDate === dateStr && p.viewType === pose);
  };

  const handleUploadSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const finalUrl =
      uploadPhotoUrl.trim() ||
      posePresets[uploadPose] ||
      'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?auto=format&fit=crop&q=80&w=500';

    addBodyPhoto({
      clientId: client.id,
      photoDate: uploadDate,
      viewType: uploadPose,
      photoUrl: finalUrl,
      notes: uploadNotes || `Foto en traje de baño - ${poseLabels[uploadPose]}`
    });

    setShowUploadModal(false);
    setUploadPhotoUrl('');
    setUploadNotes('');
  };

  const posesList: PoseType[] = ['front', 'back', 'left_side', 'right_side'];

  return (
    <div className="space-y-6">
      
      {/* Header Controls */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-xl">
        <div>
          <div className="flex items-center gap-2">
            <Camera className="w-5 h-5 text-cyan-400" />
            <h2 className="text-base font-bold text-white">
              Galería de Fotos Corporales (4 Poses)
            </h2>
          </div>
          <p className="text-xs text-slate-400 mt-1">
            4 poses en traje de baño: Frente, Espalda, Lado Izquierdo y Lado Derecho.
          </p>
        </div>

        <button
          onClick={() => setShowUploadModal(true)}
          className="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5 self-start sm:self-auto"
        >
          <Plus className="w-4 h-4 stroke-[3]" />
          Subir Fotos 4 Poses
        </button>
      </div>

      {/* Side-by-Side Comparison Selector Bar */}
      {photoDates.length > 0 && (
        <div className="bg-slate-900/80 p-4 rounded-2xl border border-slate-800 flex flex-wrap items-center justify-between gap-4">
          <div className="flex items-center gap-2 text-xs font-bold text-white">
            <Layers className="w-4 h-4 text-cyan-400" />
            <span>Comparativa Antes / Después (21 Días)</span>
          </div>

          <div className="flex flex-wrap items-center gap-3 text-xs">
            {/* Control 1 (Inicial) */}
            <div className="flex items-center gap-2">
              <span className="text-slate-400 font-medium">Control A:</span>
              <select
                value={selectedDate2}
                onChange={(e) => setSelectedDate2(e.target.value)}
                className="bg-slate-800 text-slate-200 text-xs py-1.5 px-3 rounded-lg border border-slate-700 focus:outline-none focus:border-cyan-500 font-semibold"
              >
                {photoDates.map((date) => (
                  <option key={date} value={date}>
                    {date} {date === client.startDate ? '(Inicial)' : ''}
                  </option>
                ))}
              </select>
            </div>

            <ArrowRight className="w-4 h-4 text-cyan-400 hidden sm:block" />

            {/* Control 2 (Reciente) */}
            <div className="flex items-center gap-2">
              <span className="text-slate-400 font-medium">Control B:</span>
              <select
                value={selectedDate1}
                onChange={(e) => setSelectedDate1(e.target.value)}
                className="bg-slate-800 text-slate-200 text-xs py-1.5 px-3 rounded-lg border border-slate-700 focus:outline-none focus:border-cyan-500 font-semibold"
              >
                {photoDates.map((date) => (
                  <option key={date} value={date}>
                    {date} {date === photoDates[0] ? '(Último)' : ''}
                  </option>
                ))}
              </select>
            </div>
          </div>
        </div>
      )}

      {/* 4 Poses Grid - Side by Side Comparison */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {posesList.map((pose) => {
          const photoA = getPhotoForDateAndPose(selectedDate2, pose);
          const photoB = getPhotoForDateAndPose(selectedDate1, pose);

          return (
            <div
              key={pose}
              className="bg-slate-900 border border-slate-800 rounded-2xl p-4 space-y-3 shadow-lg flex flex-col justify-between"
            >
              {/* Pose Title */}
              <div className="flex items-center justify-between pb-2 border-b border-slate-800">
                <span className="font-extrabold text-xs text-white uppercase tracking-wider">
                  {poseLabels[pose]}
                </span>
                <span className="text-[10px] text-cyan-400 font-bold bg-cyan-500/10 px-2 py-0.5 rounded">
                  4 Poses
                </span>
              </div>

              {/* Side-by-side Images for Control A vs B */}
              <div className="grid grid-cols-2 gap-2 flex-1">
                
                {/* Image A */}
                <div className="relative group rounded-xl overflow-hidden bg-slate-950 aspect-[3/4] border border-slate-800">
                  <div className="absolute top-1.5 left-1.5 z-10 bg-slate-950/80 px-1.5 py-0.5 rounded text-[9px] font-bold text-slate-300 border border-slate-700">
                    A: {selectedDate2.slice(5)}
                  </div>
                  {photoA ? (
                    <img
                      src={photoA.photoUrl}
                      alt={`${pose} ${selectedDate2}`}
                      className="w-full h-full object-cover group-hover:scale-105 transition duration-300 cursor-pointer"
                      onClick={() => setSelectedPhotoModal(photoA)}
                    />
                  ) : (
                    <div className="w-full h-full flex flex-col items-center justify-center p-2 text-center text-[10px] text-slate-500">
                      <span>Sin foto {selectedDate2}</span>
                    </div>
                  )}
                </div>

                {/* Image B */}
                <div className="relative group rounded-xl overflow-hidden bg-slate-950 aspect-[3/4] border border-cyan-500/30">
                  <div className="absolute top-1.5 left-1.5 z-10 bg-cyan-950/80 px-1.5 py-0.5 rounded text-[9px] font-bold text-cyan-300 border border-cyan-500/40">
                    B: {selectedDate1.slice(5)}
                  </div>
                  {photoB ? (
                    <img
                      src={photoB.photoUrl}
                      alt={`${pose} ${selectedDate1}`}
                      className="w-full h-full object-cover group-hover:scale-105 transition duration-300 cursor-pointer"
                      onClick={() => setSelectedPhotoModal(photoB)}
                    />
                  ) : (
                    <div className="w-full h-full flex flex-col items-center justify-center p-2 text-center text-[10px] text-slate-500">
                      <span>Sin foto {selectedDate1}</span>
                    </div>
                  )}
                </div>

              </div>

              {/* Footer Button to upload pose specifically */}
              <button
                onClick={() => {
                  setUploadPose(pose);
                  setShowUploadModal(true);
                }}
                className="w-full py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-semibold rounded-lg transition border border-slate-700 flex items-center justify-center gap-1"
              >
                <Plus className="w-3 h-3" />
                Actualizar {poseLabels[pose]}
              </button>

            </div>
          );
        })}
      </div>

      {/* Modal for full size view */}
      {selectedPhotoModal && (
        <div
          className="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4"
          onClick={() => setSelectedPhotoModal(null)}
        >
          <div
            className="bg-slate-900 border border-slate-800 rounded-2xl p-4 max-w-lg w-full space-y-4 shadow-2xl relative"
            onClick={(e) => e.stopPropagation()}
          >
            <div className="flex items-center justify-between pb-2 border-b border-slate-800">
              <div>
                <h3 className="font-bold text-sm text-white">
                  {poseLabels[selectedPhotoModal.viewType]}
                </h3>
                <p className="text-xs text-slate-400">Fecha: {selectedPhotoModal.photoDate}</p>
              </div>
              <button
                onClick={() => setSelectedPhotoModal(null)}
                className="p-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="aspect-[3/4] rounded-xl overflow-hidden bg-slate-950">
              <img
                src={selectedPhotoModal.photoUrl}
                alt="Pose Completa"
                className="w-full h-full object-contain"
              />
            </div>

            {selectedPhotoModal.notes && (
              <p className="text-xs text-slate-300 italic bg-slate-800/60 p-2.5 rounded-lg border border-slate-700/50">
                "{selectedPhotoModal.notes}"
              </p>
            )}
          </div>
        </div>
      )}

      {/* Upload 4-Pose Modal */}
      {showUploadModal && (
        <div className="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-md w-full space-y-5 shadow-2xl relative">
            
            <div className="flex items-center justify-between pb-3 border-b border-slate-800">
              <div className="flex items-center gap-2">
                <Camera className="w-5 h-5 text-cyan-400" />
                <h3 className="font-bold text-base text-white">Cargar Foto de Pose</h3>
              </div>
              <button
                onClick={() => setShowUploadModal(false)}
                className="p-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <form onSubmit={handleUploadSubmit} className="space-y-4 text-xs">
              
              {/* Pose Selector */}
              <div>
                <label className="block text-slate-300 font-semibold mb-1">Ángulo de Pose</label>
                <select
                  value={uploadPose}
                  onChange={(e) => setUploadPose(e.target.value as PoseType)}
                  className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-medium"
                >
                  <option value="front">Pose Frente (Frontal)</option>
                  <option value="back">Pose Espalda (Posterior)</option>
                  <option value="left_side">Pose Lado Izquierdo (Perfil Left)</option>
                  <option value="right_side">Pose Lado Derecho (Perfil Right)</option>
                </select>
              </div>

              {/* Date Selector */}
              <div>
                <label className="block text-slate-300 font-semibold mb-1">Fecha de la Foto</label>
                <input
                  type="date"
                  value={uploadDate}
                  onChange={(e) => setUploadDate(e.target.value)}
                  className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-medium"
                  required
                />
              </div>

              {/* Photo URL / Upload preset */}
              <div>
                <label className="block text-slate-300 font-semibold mb-1">URL de la Imagen (o Preset)</label>
                <input
                  type="text"
                  placeholder="https://o dejar en blanco para preset..."
                  value={uploadPhotoUrl}
                  onChange={(e) => setUploadPhotoUrl(e.target.value)}
                  className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-mono text-[11px]"
                />
                <p className="text-[10px] text-slate-500 mt-1">
                  Si dejas en blanco se asignará una foto fotográfica de alta resolución demostrativa.
                </p>
              </div>

              {/* Notes */}
              <div>
                <label className="block text-slate-300 font-semibold mb-1">Notas del Entrenador</label>
                <input
                  type="text"
                  placeholder="Ej: Control 21 días en traje de baño..."
                  value={uploadNotes}
                  onChange={(e) => setUploadNotes(e.target.value)}
                  className="w-full bg-slate-800 text-slate-200 py-2 px-3 rounded-xl border border-slate-700 focus:outline-none focus:border-cyan-500 font-medium"
                />
              </div>

              {/* Action Buttons */}
              <div className="pt-2 flex justify-end gap-2">
                <button
                  type="button"
                  onClick={() => setShowUploadModal(false)}
                  className="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl transition"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-extrabold rounded-xl shadow transition"
                >
                  Guardar Foto
                </button>
              </div>

            </form>

          </div>
        </div>
      )}

    </div>
  );
};
