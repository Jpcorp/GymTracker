import { InBodyMetric, BodyMeasurement } from '../types';

export function calculateBMI(weightKg: number, heightCm: number): number {
  if (!heightCm || heightCm <= 0 || !weightKg || weightKg <= 0) return 0;
  const heightM = heightCm / 100;
  const bmi = weightKg / (heightM * heightM);
  return Math.round(bmi * 10) / 10;
}

export function calculatePercentageChange(current: number, previous: number): number {
  if (!previous || previous === 0) return 0;
  const diff = ((current - previous) / previous) * 100;
  return Math.round(diff * 10) / 10;
}

export function calculateAbsoluteDifference(current: number, previous: number): number {
  return Math.round((current - previous) * 10) / 10;
}

export function getDaysBetween(date1Str: string, date2Str: string): number {
  const d1 = new Date(date1Str);
  const d2 = new Date(date2Str);
  const diffTime = Math.abs(d2.getTime() - d1.getTime());
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}

export function getNextEvaluationDate(startDateStr: string, lastEvalNumber: number = 0): {
  nextDate: string;
  daysRemaining: number;
  evalNumber: number;
} {
  const start = new Date(startDateStr);
  const nextEvalNum = lastEvalNumber + 1;
  const targetDays = nextEvalNum * 21;
  
  const nextDate = new Date(start);
  nextDate.setDate(start.getDate() + targetDays);
  
  const today = new Date();
  const diffTime = nextDate.getTime() - today.getTime();
  const daysRemaining = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  const formattedDate = nextDate.toISOString().split('T')[0];
  
  return {
    nextDate: formattedDate,
    daysRemaining,
    evalNumber: nextEvalNum
  };
}

export function getBMICategory(bmi: number): { label: string; color: string } {
  if (bmi < 18.5) return { label: 'Bajo peso', color: 'text-amber-600 bg-amber-50 border-amber-200' };
  if (bmi < 24.9) return { label: 'Peso saludable', color: 'text-emerald-600 bg-emerald-50 border-emerald-200' };
  if (bmi < 29.9) return { label: 'Sobrepeso', color: 'text-orange-600 bg-orange-50 border-orange-200' };
  return { label: 'Obesidad', color: 'text-rose-600 bg-rose-50 border-rose-200' };
}

export function getVisceralFatCategory(level: number): { label: string; color: string } {
  if (level <= 9) return { label: 'Saludable (1-9)', color: 'text-emerald-600 bg-emerald-50' };
  if (level <= 14) return { label: 'Alto (10-14)', color: 'text-amber-600 bg-amber-50' };
  return { label: 'Muy Alto (15+)', color: 'text-rose-600 bg-rose-50' };
}

export function formatTrend(delta: number, isLowerBetter: boolean = true) {
  if (delta === 0) return { symbol: '➔', text: '0', isPositive: true, color: 'text-slate-500' };
  const isGood = isLowerBetter ? delta < 0 : delta > 0;
  const symbol = delta > 0 ? '↗' : '↘';
  const prefix = delta > 0 ? '+' : '';
  return {
    symbol,
    text: `${prefix}${delta}`,
    isPositive: isGood,
    color: isGood ? 'text-emerald-600 font-semibold' : 'text-rose-600 font-semibold'
  };
}
