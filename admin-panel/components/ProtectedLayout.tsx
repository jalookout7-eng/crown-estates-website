import { requireSession } from '@/lib/auth';
import Sidebar from './Sidebar';
import TopBar from './TopBar';

interface ProtectedLayoutProps {
  children: React.ReactNode;
  title: string;
}

export default async function ProtectedLayout({ children, title }: ProtectedLayoutProps) {
  const session = await requireSession();

  return (
    <div className="min-h-screen bg-bg">
      <Sidebar role={session.role} />
      <TopBar title={title} username={session.username} />
      <main className="ml-[220px] pt-14 min-h-screen">
        <div className="p-6">{children}</div>
      </main>
    </div>
  );
}
