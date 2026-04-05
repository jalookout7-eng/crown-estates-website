'use client';

import { LineChart, Line, XAxis, YAxis, Tooltip, ResponsiveContainer } from 'recharts';

interface ChartDataPoint {
  day: string;
  count: number;
}

export default function EnquiriesChart({ data }: { data: ChartDataPoint[] }) {
  return (
    <ResponsiveContainer width="100%" height={80}>
      <LineChart data={data}>
        <XAxis dataKey="day" hide />
        <YAxis hide />
        <Tooltip
          contentStyle={{ background: '#fff', border: '1px solid #E8E8E8', borderRadius: 4, fontSize: 12 }}
        />
        <Line
          type="monotone"
          dataKey="count"
          stroke="#C4973A"
          strokeWidth={2}
          dot={false}
          activeDot={{ r: 4, fill: '#C4973A' }}
        />
      </LineChart>
    </ResponsiveContainer>
  );
}
