import { useState } from 'react'
import {
  Home, Star, Settings, Trash2, Download, Upload, Search,
  ChevronRight, Info, CheckCircle, AlertTriangle, XCircle,
  User, Mail, Lock, Bell, Eye, EyeOff, Plus, Edit,
} from 'lucide-react'
import { Button, Input, Select, Textarea, Modal, ConfirmModal, Badge, Table, Alert } from '@/components/ui'

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <section className="mb-12">
      <h2 className="text-lg font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-200">{title}</h2>
      {children}
    </section>
  )
}

function Row({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <div className="mb-6">
      <p className="text-xs font-medium text-slate-400 uppercase tracking-wide mb-3">{label}</p>
      <div className="flex flex-wrap items-center gap-3">{children}</div>
    </div>
  )
}

const tableData = [
  { id: 1, name: 'Alpha Record', code: 'ALPHA', status: 'active', created_at: '2024-01-01' },
  { id: 2, name: 'Beta Record', code: 'BETA', status: 'inactive', created_at: '2024-01-02' },
  { id: 3, name: 'Gamma Record', code: 'GAMMA', status: 'active', created_at: '2024-01-03' },
]

export function PreviewPage() {
  const [showModal, setShowModal] = useState(false)
  const [showConfirm, setShowConfirm] = useState(false)
  const [showPassword, setShowPassword] = useState(false)
  const [inputVal, setInputVal] = useState('')
  const [selectVal, setSelectVal] = useState('')

  return (
    <div className="max-w-5xl mx-auto">
      <div className="page-header">
        <div>
          <h1 className="page-title">Component Preview</h1>
          <p className="text-sm text-slate-500 mt-1">Design system showcase — light blue & white theme</p>
        </div>
      </div>

      {/* ── BUTTONS ── */}
      <Section title="Buttons">
        <Row label="Variants">
          <Button variant="primary">Primary</Button>
          <Button variant="secondary">Secondary</Button>
          <Button variant="outline">Outline</Button>
          <Button variant="ghost">Ghost</Button>
          <Button variant="danger">Danger</Button>
          <Button variant="success">Success</Button>
        </Row>
        <Row label="Sizes">
          <Button size="xs">Extra Small</Button>
          <Button size="sm">Small</Button>
          <Button size="md">Medium</Button>
          <Button size="lg">Large</Button>
        </Row>
        <Row label="With Icons">
          <Button leftIcon={<Plus size={15} />}>Create</Button>
          <Button leftIcon={<Edit size={15} />} variant="outline">Edit</Button>
          <Button leftIcon={<Trash2 size={15} />} variant="danger">Delete</Button>
          <Button leftIcon={<Download size={15} />} variant="secondary">Export</Button>
          <Button rightIcon={<ChevronRight size={15} />} variant="ghost">Continue</Button>
        </Row>
        <Row label="States">
          <Button loading>Loading</Button>
          <Button disabled>Disabled</Button>
          <Button variant="outline" disabled>Disabled Outline</Button>
        </Row>
      </Section>

      {/* ── BADGES ── */}
      <Section title="Badges">
        <Row label="Variants">
          <Badge>Default</Badge>
          <Badge variant="primary">Primary</Badge>
          <Badge variant="success">Success</Badge>
          <Badge variant="warning">Warning</Badge>
          <Badge variant="danger">Danger</Badge>
          <Badge variant="info">Info</Badge>
        </Row>
        <Row label="With Dot">
          <Badge variant="success" dot>Active</Badge>
          <Badge variant="danger" dot>Inactive</Badge>
          <Badge variant="warning" dot>Pending</Badge>
          <Badge variant="primary" dot>Processing</Badge>
        </Row>
      </Section>

      {/* ── INPUTS ── */}
      <Section title="Inputs">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
          <Input label="Basic Input" placeholder="Type something..." />
          <Input label="With Left Icon" placeholder="Search..." leftAddon={<Search size={15} />} />
          <Input
            label="Password"
            type={showPassword ? 'text' : 'password'}
            placeholder="••••••••"
            leftAddon={<Lock size={15} />}
            rightAddon={
              <button onClick={() => setShowPassword((v) => !v)}>
                {showPassword ? <EyeOff size={15} /> : <Eye size={15} />}
              </button>
            }
          />
          <Input
            label="With Hint"
            placeholder="yourname"
            hint="Only letters, numbers and underscores"
          />
          <Input label="Required Field" placeholder="Cannot be empty" required />
          <Input label="Error State" placeholder="Invalid value" error="This field is required" />
          <Input label="Disabled" placeholder="Cannot edit" disabled value="Locked value" onChange={() => {}} />
          <Input
            label="Controlled"
            value={inputVal}
            onChange={(e) => setInputVal(e.target.value)}
            placeholder="Controlled input"
            hint={`Characters: ${inputVal.length}`}
          />
        </div>
      </Section>

      {/* ── SELECTS ── */}
      <Section title="Selects">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
          <Select
            label="Status"
            options={[
              { value: 'active', label: 'Active' },
              { value: 'inactive', label: 'Inactive' },
            ]}
            placeholder="Choose status..."
            value={selectVal}
            onChange={(e) => setSelectVal(e.target.value)}
          />
          <Select
            label="Role"
            options={[
              { value: 'admin', label: 'Administrator' },
              { value: 'editor', label: 'Editor' },
              { value: 'viewer', label: 'Viewer' },
            ]}
            placeholder="Select role..."
          />
          <Select
            label="With Error"
            options={[{ value: 'a', label: 'Option A' }]}
            error="Please select a value"
            placeholder="Select..."
          />
          <Select
            label="Disabled"
            options={[{ value: 'a', label: 'Option A' }]}
            disabled
            value="a"
            onChange={() => {}}
          />
        </div>
      </Section>

      {/* ── TEXTAREA ── */}
      <Section title="Textarea">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
          <Textarea label="Notes" placeholder="Enter your notes..." />
          <Textarea label="With Hint" placeholder="Describe the issue..." hint="Max 500 characters" />
          <Textarea label="Error State" placeholder="Required" error="This field is required" />
          <Textarea label="Disabled" disabled value="Cannot be changed" onChange={() => {}} />
        </div>
      </Section>

      {/* ── FORMS ── */}
      <Section title="Form Example">
        <div className="card max-w-lg p-6">
          <h3 className="font-semibold text-slate-900 mb-4">Create New Entry</h3>
          <form className="space-y-4" onSubmit={(e) => e.preventDefault()}>
            <div className="grid grid-cols-2 gap-4">
              <Input label="First Name" placeholder="John" required leftAddon={<User size={15} />} />
              <Input label="Last Name" placeholder="Doe" required />
            </div>
            <Input label="Email Address" type="email" placeholder="john@example.com" required leftAddon={<Mail size={15} />} />
            <Select
              label="Account Type"
              options={[
                { value: 'personal', label: 'Personal' },
                { value: 'business', label: 'Business' },
              ]}
              placeholder="Select type..."
              required
            />
            <Textarea label="Additional Notes" placeholder="Any extra information..." />
            <div className="flex gap-3 pt-2">
              <Button type="submit" leftIcon={<Plus size={15} />}>Create Entry</Button>
              <Button type="button" variant="outline">Cancel</Button>
            </div>
          </form>
        </div>
      </Section>

      {/* ── ALERTS ── */}
      <Section title="Alerts">
        <div className="space-y-3 max-w-2xl">
          <Alert variant="info" title="Information">Your account is set up and ready to use.</Alert>
          <Alert variant="success" title="Success">Record has been saved successfully.</Alert>
          <Alert variant="warning" title="Warning">Your session will expire in 5 minutes.</Alert>
          <Alert variant="error" title="Error">Failed to connect to the server.</Alert>
          <Alert variant="info" dismissible>This alert can be dismissed.</Alert>
        </div>
      </Section>

      {/* ── MODALS ── */}
      <Section title="Modals">
        <Row label="Trigger">
          <Button onClick={() => setShowModal(true)} leftIcon={<Eye size={15} />}>Open Modal</Button>
          <Button variant="danger" onClick={() => setShowConfirm(true)} leftIcon={<Trash2 size={15} />}>Confirm Delete</Button>
        </Row>

        <Modal
          open={showModal}
          onClose={() => setShowModal(false)}
          title="Example Modal"
          footer={
            <>
              <Button variant="outline" onClick={() => setShowModal(false)}>Cancel</Button>
              <Button onClick={() => setShowModal(false)}>Confirm</Button>
            </>
          }
        >
          <p className="text-slate-600 text-sm mb-4">
            This is a sample modal with a header, scrollable body, and footer action buttons.
          </p>
          <Input label="Value" placeholder="Enter something..." />
        </Modal>

        <ConfirmModal
          open={showConfirm}
          onClose={() => setShowConfirm(false)}
          onConfirm={() => setShowConfirm(false)}
          title="Confirm Deletion"
          message="Are you sure you want to delete this item? This action is permanent."
          confirmLabel="Delete"
        />
      </Section>

      {/* ── TABLE ── */}
      <Section title="Table">
        <Table
          columns={[
            { key: 'name', header: 'Name' },
            { key: 'code', header: 'Code', className: 'font-mono text-xs' },
            {
              key: 'status',
              header: 'Status',
              render: (row) => (
                <Badge variant={(row as { status: string }).status === 'active' ? 'success' : 'default'} dot>
                  {(row as { status: string }).status}
                </Badge>
              ),
            },
            { key: 'created_at', header: 'Created' },
          ]}
          data={tableData as unknown as Record<string, unknown>[]}
          keyField="id"
        />
      </Section>

      {/* ── ICONS ── */}
      <Section title="Icons (Lucide)">
        <div className="flex flex-wrap gap-4">
          {[Home, Star, Settings, Trash2, Download, Upload, Search, Info, CheckCircle,
            AlertTriangle, XCircle, User, Mail, Lock, Bell, Eye, EyeOff, Plus, Edit,
            ChevronRight].map((Icon, i) => (
            <div key={i} className="flex flex-col items-center gap-1.5 p-3 rounded-lg bg-slate-50 border border-slate-200 w-16">
              <Icon size={20} className="text-primary-500" />
              <span className="text-xs text-slate-400 text-center truncate w-full">{Icon.displayName || Icon.name}</span>
            </div>
          ))}
        </div>
      </Section>

      {/* ── THEME VARIATIONS ── */}
      <Section title="Theme Variations">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          {/* Light */}
          <div className="rounded-xl border border-slate-200 bg-white p-5 space-y-3">
            <div className="w-8 h-8 bg-primary-500 rounded-lg" />
            <h3 className="font-semibold text-slate-900 text-sm">Light Theme</h3>
            <p className="text-xs text-slate-500">White + Light Blue — default</p>
            <Badge variant="primary">Active</Badge>
            <div className="pt-1"><Button size="sm">Action</Button></div>
          </div>

          {/* Slate */}
          <div className="rounded-xl border border-slate-700 bg-slate-900 p-5 space-y-3">
            <div className="w-8 h-8 bg-primary-400 rounded-lg" />
            <h3 className="font-semibold text-white text-sm">Dark Theme</h3>
            <p className="text-xs text-slate-400">Slate 900 + Blue — alternative</p>
            <span className="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-0.5 rounded-full bg-primary-900 text-primary-300">
              <span className="w-1.5 h-1.5 rounded-full bg-primary-400" /> Active
            </span>
            <div className="pt-1"><Button size="sm" variant="secondary">Action</Button></div>
          </div>

          {/* Soft */}
          <div className="rounded-xl border border-primary-200 bg-primary-50 p-5 space-y-3">
            <div className="w-8 h-8 bg-primary-600 rounded-lg" />
            <h3 className="font-semibold text-primary-900 text-sm">Soft Theme</h3>
            <p className="text-xs text-primary-600">Sky 50 + Deep Blue — soft</p>
            <Badge variant="info">Active</Badge>
            <div className="pt-1"><Button size="sm" variant="secondary">Action</Button></div>
          </div>
        </div>
      </Section>

      {/* ── TYPOGRAPHY ── */}
      <Section title="Typography">
        <div className="space-y-3 max-w-2xl">
          <h1 className="text-3xl font-bold text-slate-900">Heading 1 — Bold 3xl</h1>
          <h2 className="text-2xl font-semibold text-slate-900">Heading 2 — Semibold 2xl</h2>
          <h3 className="text-xl font-semibold text-slate-800">Heading 3 — Semibold xl</h3>
          <h4 className="text-lg font-medium text-slate-800">Heading 4 — Medium lg</h4>
          <p className="text-base text-slate-700">Body text — base size, slate-700</p>
          <p className="text-sm text-slate-600">Small text — sm size, slate-600</p>
          <p className="text-xs text-slate-500">Caption — xs size, slate-500</p>
          <p className="text-sm text-primary-600 font-medium">Link text — primary-600, medium</p>
          <code className="text-sm font-mono bg-slate-100 text-slate-700 px-2 py-0.5 rounded">
            code snippet — mono
          </code>
        </div>
      </Section>

      {/* ── LAYOUT GRID ── */}
      <Section title="Layout Grid">
        <div className="space-y-3">
          <div className="grid grid-cols-12 gap-2">
            {Array.from({ length: 12 }).map((_, i) => (
              <div key={i} className="h-8 bg-primary-100 rounded text-center text-xs text-primary-600 flex items-center justify-center font-mono">
                {i + 1}
              </div>
            ))}
          </div>
          <div className="grid grid-cols-3 gap-4">
            {['1/3', '1/3', '1/3'].map((label, i) => (
              <div key={i} className="h-20 bg-primary-50 border border-primary-200 rounded-xl flex items-center justify-center text-sm text-primary-600 font-medium">
                col-{label}
              </div>
            ))}
          </div>
          <div className="grid grid-cols-4 gap-4">
            {['1/4', '2/4', '3/4', '4/4'].map((label, i) => (
              <div key={i} className="h-14 bg-slate-100 border border-slate-200 rounded-xl flex items-center justify-center text-xs text-slate-500">
                col-{label}
              </div>
            ))}
          </div>
        </div>
      </Section>
    </div>
  )
}
