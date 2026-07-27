export type Gender = 'male' | 'female' | 'other';
export type ClientStatus = 'active' | 'paused' | 'inactive';
export type PoseType = 'front' | 'back' | 'left_side' | 'right_side';
export type SessionType = 'personal' | 'group' | 'free';
export type NutritionCompliance = 'complete' | 'partial' | 'missed';

export interface Client {
  id: string;
  name: string;
  email: string;
  phone: string;
  birthDate: string; // YYYY-MM-DD
  gender: Gender;
  startDate: string; // YYYY-MM-DD
  heightCm: number;
  goal: string;
  profilePhoto?: string;
  status: ClientStatus;
  trainerName: string;
  notes?: string;
}

export interface InBodyMetric {
  id: string;
  clientId: string;
  recordedAt: string; // YYYY-MM-DD
  weightKg: number;
  bodyFatPercentage: number;
  bmi: number; // calculated: weight / (height/100)^2
  metabolicAge: number;
  basalKcal: number;
  visceralFat: number;
  evaluationId?: string;
}

export interface BodyMeasurement {
  id: string;
  clientId: string;
  recordedAt: string; // YYYY-MM-DD
  waistCm: number;
  hipsCm: number;
  chestCm: number;
  rightArmCm: number;
  leftArmCm: number;
  rightThighCm: number;
  leftThighCm: number;
  evaluationId?: string;
}

export interface BodyPhoto {
  id: string;
  clientId: string;
  photoDate: string; // YYYY-MM-DD
  viewType: PoseType;
  photoUrl: string;
  evaluationId?: string;
  notes?: string;
}

export interface Exercise {
  id: string;
  routineId: string;
  name: string;
  muscleGroup: string;
  sets: number;
  repsRange: string;
  restSeconds: number;
  notes?: string;
}

export interface Routine {
  id: string;
  clientId: string;
  name: string;
  description: string;
  weeklyFrequency: number;
  startDate: string;
  endDate?: string;
  isActive: boolean;
  exercises: Exercise[];
}

export interface WorkoutLog {
  id: string;
  clientId: string;
  exerciseId: string;
  exerciseName: string;
  workoutDate: string;
  // Weight for 1st Base Set (15 reps)
  baseSetWeightKg: number;
  // Weight for 3rd Effective Set (8-10 reps max load)
  effectiveSetWeightKg: number;
  completedSets: number;
  completedReps: string;
  rpe?: number; // 1-10 rate of perceived exertion
  notes?: string;
}

export interface Attendance {
  id: string;
  clientId: string;
  attendanceDate: string; // YYYY-MM-DD
  checkIn: string; // HH:MM
  checkOut?: string; // HH:MM
  sessionType: SessionType;
  durationMinutes: number;
}

export interface Evaluation {
  id: string;
  clientId: string;
  evaluationNumber: number; // 1, 2, 3...
  periodStart: string; // YYYY-MM-DD
  periodEnd: string; // YYYY-MM-DD
  evaluatedAt: string; // YYYY-MM-DD
  achievementsSummary: string[];
  trainerNotes: string;
  inBodyMetricId: string;
  measurementId: string;
}

export interface MoodRecord {
  id: string;
  clientId: string;
  weekStart: string;
  weekEnd: string;
  moodLevel: number; // 1-10
  energyLevel: number; // 1-10
  motivationLevel: number; // 1-10
  notes?: string;
}

export interface NutritionLog {
  id: string;
  clientId: string;
  logDate: string;
  compliance: NutritionCompliance;
  mealsLogged: number;
  mealsPlanned: number;
  notes?: string;
}

export interface SatisfactionSurvey {
  id: string;
  clientId: string;
  surveyDate: string;
  overallSatisfaction: number; // 1-10
  trainerSatisfaction: number;
  facilitiesSatisfaction: number;
  routinesSatisfaction: number;
  comments?: string;
}
