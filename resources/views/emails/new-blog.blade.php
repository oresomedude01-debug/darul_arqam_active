<x-mail::message>
# 📰 New Blog Post Published

Hello,

A new blog post has been published on {{ $schoolName }}!

## {{ $blog->title }}

**Category:** {{ $blog->category_label }}  
**Author:** {{ $blog->author->name }}

---

{{ $blog->excerpt }}

---

<x-mail::button :url="route('blog.show', $blog->slug)">
Read Full Article
</x-mail::button>

---

*Stay updated with our latest blog posts. Visit our blog page to read more articles.*

Thanks,  
{{ $schoolName }} Team
</x-mail::message>
